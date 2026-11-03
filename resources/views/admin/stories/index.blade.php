@extends('admin.layouts.app')
 
@section('title', 'Stories | nado.ma')

@section('content')
    <div id="main-content">
        <div class="page-heading">
            <h3>Stories</h3>
        </div>
        <div class="page-content">
            <section class="row">
                <div class="row" id="table-striped">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="card-title">List Stories</h4>
                                <div class="d-flex" style="gap:8px;">
                                    <a href="{{ route('admin.export', 'stories') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
                                    <a href="{{ route('admin.stories.create') }}" class="btn btn-primary">Add New Story</a>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="table-responsive mx-4 my-2">
                                        <table class="table table-striped mb-0 display" id="example">
                                            <thead>
                                                <tr>
                                                    <th>Media</th>
                                                    <th>Type</th>
                                                    <th>Instagram</th>
                                                    <th>Views</th>
                                                    <th>Expires At</th>
                                                    <th class="d-flex justify-content-end">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($stories as $story)
                                                    @php
                                                        // /p/, /reel/, /reels/ and /tv/ all embed via {type}/{code}/embed
                                                        $instaEmbed = '';
                                                        if (
                                                            !empty($story->instagram_link) &&
                                                            preg_match('#instagram\.com/(p|reel|reels|tv)/([A-Za-z0-9_-]+)#', $story->instagram_link, $m)
                                                        ) {
                                                            $instaEmbed =
                                                                'https://www.instagram.com/' .
                                                                ($m[1] === 'reels' ? 'reel' : $m[1]) .
                                                                '/' . $m[2] . '/embed';
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td width="10%">
                                                            {{-- DEBUG: Show the actual path --}}
                                                            <!-- <small>Path: {{ $story->media_path }}</small> -->
                                                            @if (empty($story->media_path) && $instaEmbed)
                                                                {{-- Link-only story: play the Instagram post inline. --}}
                                                                <iframe src="{{ $instaEmbed }}" width="260"
                                                                    height="330" frameborder="0" scrolling="no"
                                                                    allowtransparency="true" allowfullscreen
                                                                    loading="lazy"
                                                                    style="border:0;border-radius:8px;"></iframe>
                                                            @elseif (empty($story->media_path))
                                                                <span class="text-muted">Aucun média</span>
                                                            @elseif ($story->media_type == 'image')
                                                                {{-- FIX: Removed 'stories/' because $story->media_path already has it --}}
                                                                <img src="{{ asset('storage/' . $story->media_path) }}"
                                                                    alt="Story" width="80">
                                                            @else
                                                                {{-- FIX: Same here for video --}}
                                                                <video src="{{ asset('storage/' . $story->media_path) }}"
                                                                    width="100%" controls></video>
                                                            @endif
                                                        </td>
                                                        {{-- ucfirst(null) is deprecated on PHP 8.2 --}}
                                                        <td>{{ $story->media_type ? ucfirst($story->media_type) : 'Lien' }}</td>
                                                        <td>
                                                            @if ($story->instagram_link)
                                                                <a href="{{ $story->instagram_link }}" target="_blank"
                                                                    rel="noopener noreferrer"
                                                                    title="{{ $story->instagram_link }}"
                                                                    style="display:inline-flex;align-items:center;gap:6px;color:#DD2A7B;text-decoration:none;font-weight:600;">
                                                                    <svg width="16" height="16" viewBox="0 0 24 24"
                                                                        fill="none" stroke="currentColor"
                                                                        stroke-width="2" stroke-linecap="round"
                                                                        stroke-linejoin="round">
                                                                        <rect x="2" y="2" width="20" height="20" rx="5"
                                                                            ry="5" />
                                                                        <path
                                                                            d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                                                                        <line x1="17.5" y1="6.5" x2="17.51"
                                                                            y2="6.5" />
                                                                    </svg>
                                                                    Voir
                                                                </a>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $story->views }}</td>
                                                        <td>{{ $story->expires_at }}</td>
                                                        <td>
                                                            <div class="d-flex gap-2 w-100 justify-content-end">
                                                                <form
                                                                    action="{{ route('admin.stories.destroy', $story->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                                        onclick="return confirm('Are you sure?')">Delete</button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
