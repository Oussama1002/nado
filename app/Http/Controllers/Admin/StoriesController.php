<?php
 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class StoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /* =======================
     * LIST STORIES
     * ======================= */
    public function index()
    {
        // Pinned stories first (slot 1, 2, 3), then the rest newest-first.
        $stories = Story::orderByRaw('pin_position IS NULL')
            ->orderBy('pin_position')
            ->orderByDesc('id')
            ->get();
        return view('admin.stories.index', compact('stories'));
    }

    /* =======================
     * CREATE FORM
     * ======================= */
    public function create()
    {
        return view('admin.stories.create');
    }

    /* =======================
     * EDIT FORM
     * ======================= */
    public function edit($id)
    {
        $story = Story::findOrFail($id);
        return view('admin.stories.edit', compact('story'));
    }

    /* =======================
     * UPDATE STORY
     * ======================= */
    public function update(Request $request, $id)
    {
        $story = Story::findOrFail($id);

        $request->validate([
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:20480',
            'expires_at' => 'nullable|date',
            'instagram_link' => 'nullable|url|max:2048',
            'pin_position' => 'nullable|integer|min:1|max:3',
        ]);

        // Replace the media only when a new file is uploaded; otherwise keep the current one.
        if ($request->hasFile('media')) {
            if ($story->media_path && Storage::disk('public')->exists($story->media_path)) {
                Storage::disk('public')->delete($story->media_path);
            }
            $story->media_path = $this->saveImage($request->file('media'), 'story');
            $story->media_type = 'image';
        }

        $story->instagram_link = $request->input('instagram_link');
        $story->expires_at = $request->expires_at ?: null;
        $this->assignPin($story, $request->filled('pin_position') ? (int) $request->pin_position : null);

        $story->save();
        Cache::forget('stories');

        return redirect()
            ->route('admin.stories.index')
            ->with('success', 'Story updated successfully');
    }

    /* =======================
     * PIN / UNPIN STORY (slot 1, 2 or 3)
     * ======================= */
    public function pin(Request $request, $id)
    {
        $story = Story::findOrFail($id);
        $request->validate([
            'pin_position' => 'nullable|integer|min:1|max:3',
        ]);

        $this->assignPin($story, $request->filled('pin_position') ? (int) $request->pin_position : null);
        $story->save();
        Cache::forget('stories');

        return redirect()
            ->route('admin.stories.index')
            ->with('success', 'Épinglage mis à jour.');
    }

    /**
     * Set a story's pin slot, keeping each slot (1/2/3) unique: if another story
     * already holds the target slot, it is freed. Does not persist $story itself.
     */
    private function assignPin(Story $story, ?int $position): void
    {
        if ($position !== null) {
            Story::where('id', '!=', $story->id)
                ->where('pin_position', $position)
                ->update(['pin_position' => null]);
        }
        $story->pin_position = $position;
    }

    /* =======================
     * STORE STORY
     * ======================= */
    public function store(Request $request)
    {
        $data = $request->validate([
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:20480',
            'expires_at' => 'nullable|date',
            'instagram_link' => 'nullable|url|max:2048',
        ]);

        $instagramLink = $request->input('instagram_link');

        if ($request->hasFile('media')) {
            $data['media_path'] = $this->saveImage(
                $request->file('media'),
                'story'
            );
            $data['media_type'] = 'image';
        } else {
            $data['media_path'] = null;
            $data['media_type'] = null;

            // No file uploaded but an Instagram link was given: try to pull the post's
            // MP4 down to our own storage so the front-end can play it natively instead
            // of iframing Instagram. Best effort - Instagram blocks this often, and the
            // front-end falls back to the embed when there is no local file.
            if ($instagramLink) {
                $downloaded = $this->fetchInstagramMedia($instagramLink);
                if ($downloaded) {
                    $data['media_path'] = $downloaded['path'];
                    $data['media_type'] = $downloaded['type'];
                }
            }
        }

        // The uploaded file itself is not a column.
        unset($data['media']);

        $data['instagram_link'] = $instagramLink;
        $data['user_id'] = Auth::id();
        $data['views'] = 0;
        // No automatic 24h expiry: a blank field means the story stays until it is
        // deleted. Set a date in the form only when it really should disappear.
        $data['expires_at'] = $request->expires_at ?: null;

        Story::create($data);

        Cache::forget('stories');

        // Tell the admin which path was taken: a local MP4 plays natively, otherwise the
        // front-end falls back to the Instagram embed.
        if ($instagramLink && !$request->hasFile('media')) {
            $message = match ($data['media_type']) {
                'video' => 'Story créée. La vidéo Instagram a été téléchargée et sera lue directement sur le site.',
                'image' => "Story créée. La vidéo n'a pas pu être téléchargée, mais la miniature Instagram a été "
                    . "récupérée : elle s'affichera sur la story et le clic ouvrira la publication intégrée.",
                default => "Story créée. Instagram a bloqué le téléchargement (post privé ou protection) : "
                    . "le site affichera la publication intégrée à la place.",
            };

            return redirect()->route('admin.stories.index')->with('success', $message);
        }

        return redirect()
            ->route('admin.stories.index')
            ->with('success', 'Story created successfully');
    }

    /* =======================
     * DELETE STORY
     * ======================= */
    public function destroy($id)
    {
        $story = Story::findOrFail($id);

        // Delete media file
        if ($story->media_path && Storage::disk('public')->exists($story->media_path)) {
            Storage::disk('public')->delete($story->media_path);
        }

        $story->delete();

        Cache::forget('stories');

        return redirect()
            ->route('admin.stories.index')
            ->with('success', 'Story deleted successfully');
    }

    /* =======================
     * INSTAGRAM VIDEO DOWNLOAD
     * =======================
     * Best effort: resolve an Instagram post URL to its MP4 and store it locally so the
     * front-end can play it as a normal <video>.
     *
     * Instagram does NOT offer a public API for this. We read the post's public HTML and
     * look for the CDN url it exposes. It works for public posts, but Instagram changes
     * its markup and shows login walls, so this can and will fail - in which case we
     * return null and the front-end keeps using the iframe embed.
     *
     * Returns a path relative to the public disk, e.g. "stories/insta-ABC123.mp4".
     */
    private function fetchInstagramMedia(string $url): ?array
    {
        if (!preg_match('#instagram\.com/(p|reel|reels|tv)/([A-Za-z0-9_-]+)#', $url, $m)) {
            return null;
        }

        $type = $m[1] === 'reels' ? 'reel' : $m[1];
        $code = $m[2];

        // Several surfaces expose the media urls; try the cheapest first.
        $candidates = [
            "https://www.instagram.com/{$type}/{$code}/embed/captioned/",
            "https://www.instagram.com/{$type}/{$code}/embed/",
            "https://www.instagram.com/{$type}/{$code}/",
        ];

        $videoUrl = null;
        $imageUrl = null;

        foreach ($candidates as $pageUrl) {
            $html = $this->httpGet($pageUrl);
            if (!$html) {
                continue;
            }

            // --- video ---
            if (!$videoUrl && preg_match('#"video_url"\s*:\s*"([^"]+)"#', $html, $v)) {
                $videoUrl = json_decode('"' . $v[1] . '"');
            } elseif (!$videoUrl && preg_match('#property=["\']og:video(?::secure_url)?["\']\s+content=["\']([^"\']+)["\']#i', $html, $v)) {
                $videoUrl = html_entity_decode($v[1]);
            }

            // --- poster / thumbnail, used when the video cannot be fetched ---
            if (!$imageUrl && preg_match('#"display_url"\s*:\s*"([^"]+)"#', $html, $i)) {
                $imageUrl = json_decode('"' . $i[1] . '"');
            } elseif (!$imageUrl && preg_match('#property=["\']og:image["\']\s+content=["\']([^"\']+)["\']#i', $html, $i)) {
                $imageUrl = html_entity_decode($i[1]);
            }

            if ($videoUrl) {
                break;
            }
        }

        // 1. Preferred: the real MP4, played natively by the front-end.
        if ($videoUrl && filter_var($videoUrl, FILTER_VALIDATE_URL)) {
            $binary = $this->httpGet($videoUrl, 60);
            // Guard against an error page being saved as a .mp4
            if ($binary && strlen($binary) >= 100 * 1024) {
                $filename = 'insta-' . $code . '.mp4';
                Storage::disk('public')->put('stories/' . $filename, $binary);
                return ['path' => 'stories/' . $filename, 'type' => 'video'];
            }
        }

        // 2. Fallback: the post thumbnail, so the story tile shows the real frame
        //    instead of a generic placeholder. Clicking still opens the embed.
        if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $binary = $this->httpGet($imageUrl, 30);
            if ($binary && strlen($binary) >= 5 * 1024) {
                $filename = 'insta-' . $code . '.jpg';
                Storage::disk('public')->put('stories/' . $filename, $binary);
                return ['path' => 'stories/' . $filename, 'type' => 'image'];
            }
        }

        return null;
    }

    /**
     * Small cURL wrapper with a browser-ish User-Agent.
     */
    private function httpGet(string $url, int $timeout = 20): ?string
    {
        if (!function_exists('curl_init')) {
            return null;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 '
                . '(KHTML, like Gecko) Chrome/122.0 Safari/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept-Language: en-US,en;q=0.9',
                'Accept: text/html,application/xhtml+xml,video/mp4,*/*;q=0.8',
            ],
        ]);

        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($body !== false && $status >= 200 && $status < 300) ? $body : null;
    }

    /* =======================
     * IMAGE SAVE & RESIZE
     * ======================= */
    private function saveImage($file, $prefix)
    {
        // 1. Generate your custom filename
        $filename = Str::slug($prefix) . '-' . time() . '.' . $file->getClientOriginalExtension();

        // 2. Save directly to the 'public' disk (which we fixed in config)
        // usage: storeAs(folder, filename, disk)
        $file->storeAs('stories', $filename, 'public');

        // 3. Return path for database
        return 'stories/' . $filename;
    }
}
