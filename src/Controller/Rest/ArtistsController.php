<?php

namespace App\Controller\Rest;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Song;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ArtistsController extends FOSRestController
{
    public function getArtist(?string $token): View
    {
        $artists = $this->getDoctrine()
            ->getRepository(Artist::class)
            ->findByToken($token);

        foreach ($artists as $key => $artist) {
            $artists[$key]['albums'] = $this->getDoctrine()
                ->getRepository(Album::class)
                ->findByArtist($artist{'id'});
        }

        return View::create($artists, Response::HTTP_OK);
    }

    public function getAlbums(?string $token): View
    {
        $albums = $this->getDoctrine()
            ->getRepository(Album::class)
            ->findByToken($token);

        foreach ($albums as $key => $album) {
            $albums[$key]['songs'] = $this->getDoctrine()
                ->getRepository(Song::class)
                ->findByAlbum($album{'id'});
        }

        return View::create($albums, Response::HTTP_OK);
    }
}