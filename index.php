<?php

Kirby::plugin('gemini-services/generate-video-poster', [
    'fields' => [
        'generatevideoposter' => [
            'props' => [
                'pageid' => function () {
                    return $this->model()->page()->id();
                },
                'basename' => function () {
                    return $this->model()->name();
                },
                'extension' => function () {
                    return $this->model()->extension();
                },
                'url' => function () {
                    return $this->model()->url();
                },
                'isposterrequired' => function () {
                    return $this->model()->content()->isposterrequired()->value();
                },
                'posterfieldname' => function ($posterfieldname = null) {
                    return $posterfieldname ?? 'poster';
                }
            ]
        ]
    ],
    'hooks' => [
        'file.create:after' => function ($file) {
            if ($file->type() === 'video') {
                $file->update([
                    'isposterrequired' => 1
                ]);
            }
        },
        'file.replace:after' => function ($file) {
            if ($file->type() === 'video') {
                $file->update([
                    'isposterrequired' => 1
                ]);
            }
        }
    ],
    'routes' => [
        [
            'pattern' => 'generate-video-poster/upload',
            'method' => 'POST',
            'action' => function () {
                $pageId = get('pageId');
                $page = page($pageId);
                if (!$page) {
                    return ['error' => '(Generate Video Poster) Page not found'];
                }

                $videoFilename = get('videoFilename');
                $video   = $page->file($videoFilename);
                if (!$video) {
                    return ['error' => '(Generate Video Poster) Video not found'];
                }

                $posterFile = $_FILES['posterFile'] ?? null;                
                if (!$posterFile || $posterFile['error'] !== UPLOAD_ERR_OK) {
                    return ['error' => '(Generate Video Poster) No file uploaded or upload error'];
                }
                
                $posterFilename = get('posterFilename');
                if (!$posterFilename) {
                    return ['error' => '(Generate Video Poster) Poster Filename missing'];
                }

                $posterFieldName = get('posterFieldname') ?? 'poster';

                try {
                    $posterFilename = $posterFilename ?? $posterFile['name'];

                    $posterFileExisiting = $page->file($posterFilename);

                    if ($posterFileExisiting) {
                        $video->update([
                            $posterFieldName => $posterFileExisiting->id(),
                            'isposterrequired' => 0
                        ]);
                    } else {
                        $posterFileUploaded = $page->createFile([
                            'source'   => $posterFile['tmp_name'],
                            'filename' => $posterFilename,
                            'template' => 'image'
                        ]);

                        $video->update([
                            $posterFieldName => $posterFileUploaded->id(),
                            'isposterrequired' => 0
                        ]);
                    }

                    return [
                        'success' => true,
                        'reload'  => true,
                    ];
                } catch (Exception $e) {
                    return [
                        'error'   => '(Generate Video Poster) ' . $e->getMessage()
                    ];
                }
            }
        ]
    ]
]);
