<?php

namespace App\Http\Controllers;

use App\Models\Annonces;
use App\Models\Article;
use App\Models\ModelGuide;
use Illuminate\Support\Facades\Schema;

/**
 * Sitemap XML : pages statiques + annonces actives + articles + guides modèles.
 * Léger, sans dépendance. Les tables récentes sont testées (hasTable) pour ne
 * jamais échouer avant une migration.
 */
class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];
        $add = function ($loc, $lastmod = null, $priority = '0.6') use (&$urls) {
            $urls[] = ['loc' => $loc, 'lastmod' => $lastmod, 'priority' => $priority];
        };

        // Pages clés.
        $add(route('index'), null, '1.0');
        $add(route('achat.index'), null, '0.9');
        if (app('router')->has('guides.index') && feature_on('community.model_guides')) {
            $add(route('guides.index'), null, '0.8');
        }
        if (app('router')->has('articles.index')) {
            $add(route('articles.index'), null, '0.7');
        }

        // Annonces actives (limite raisonnable).
        Annonces::where('active', 1)->latest()->limit(2000)->get(['id', 'mark', 'modele', 'updated_at'])
            ->each(function ($a) use ($add) {
                $add(route('achat.detail', [
                    'marque' => str_replace(' ', '', $a->mark),
                    'modele' => str_replace(' ', '', $a->modele),
                    'id'     => $a->id,
                ]), optional($a->updated_at)->toAtomString(), '0.6');
            });

        // Articles.
        Article::where('is_active', 1)->latest()->limit(1000)->get(['slug', 'updated_at'])
            ->each(function ($art) use ($add) {
                if (app('router')->has('articles.show')) {
                    $add(route('articles.show', $art->slug), optional($art->updated_at)->toAtomString(), '0.6');
                }
            });

        // Guides modèles.
        if (Schema::hasTable('model_guides') && app('router')->has('guides.show')) {
            ModelGuide::where('is_active', 1)->latest()->limit(1000)->get(['slug', 'updated_at'])
                ->each(function ($g) use ($add) {
                    $add(route('guides.show', $g->slug), optional($g->updated_at)->toAtomString(), '0.7');
                });
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= '  <url><loc>' . htmlspecialchars($u['loc'], ENT_XML1) . '</loc>'
                . ($u['lastmod'] ? '<lastmod>' . $u['lastmod'] . '</lastmod>' : '')
                . '<priority>' . $u['priority'] . '</priority></url>' . "\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
