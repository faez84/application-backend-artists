<?php

namespace App\DataFixtures;


use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Song;
use App\Utils\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Config\Definition\Exception\Exception;

class AppFixtures extends Fixture
{
    /** @var  ObjectManager $manager */
    private $manager;

    private $tokens = [];
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $str = file_get_contents(
            'https://gist.githubusercontent.com/fightbulc/9b8df4e22c2da963cf8ccf96422437fe/'.
            'raw/8d61579f7d0b32ba128ffbf1481e03f4f6722e17/artist-albums.json'
        );
        $dataArtists = json_decode($str, true);

        foreach ($dataArtists as $dataArtist) {
            $this->loadArtists($dataArtist);
        }
        $this->manager->flush();
    }

    /**
     * @param array $dataArtist
     */
    public function loadArtists(array $dataArtist)
    {
        while (true) {
            $token = TokenGenerator::generate();
            //another solution: we can check it in cache array instead of quering databas every time
            /*
            if (isset($this->tokens[$token])) {
                   continue;
            }
            
            $this->tokens[$token] = $token;
            */
            $searchedArtist = $this->manager
                ->getRepository(Artist::class)
                ->findBy(['token' => $token]);
            if (!empty($searchedArtist)) {
                continue;
            }
            /** @var Artist $artist */
            $artist = new Artist();
            $artist->setName($dataArtist['name']);
            $artist->setToken(strval($token));

            $this->manager->persist($artist);
            break;
        }

        $dataAlbums = $dataArtist['albums'];
        foreach ($dataAlbums as $dataAlbum) {
            $this->loadAlbums($dataAlbum, $artist);
        }
    }

    /**
     * @param array $dataAlbum
     * @param Artist $artist
     */
    public function loadAlbums(array $dataAlbum, Artist $artist)
    {
        while (true) {
            $token = TokenGenerator::generate();
            $searchedArtist = $this->manager
                ->getRepository(Album::class)
                ->findBy(['token' => $token]);
            if (!empty($searchedArtist)) {
                continue;
            }
            /** @var Album $album */
            $album = new Album();
            $album->setTitle($dataAlbum['title']);
            $album->setCover($dataAlbum['cover']);
            $album->setDescription(($dataAlbum['description']));
            $album->setArtistId($artist);
            $album->setToken($token);
            $this->manager->persist($album);
            break;
        }
        $dataSongs = $dataAlbum['songs'];
        foreach ($dataSongs as $dataSong) {
            $this->loadSongs($dataSong, $album);
        }
    }

    /**
     * @param array $dataSong
     * @param Album $album
     */
    public function loadSongs(array $dataSong, Album $album)
    {
        /** @var Song $song */
        $song = new Song();
        $song->setTitle($dataSong['title']);
        $length = intval((new \DateTime($dataSong['length']))->format('H'))*60
            + intval((new \DateTime($dataSong['length']))->format('i'));

        $song->setLength(($length));
        $song->setAlbumId($album);
        $this->manager->persist($song);
    }
}
