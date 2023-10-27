<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    // get pada tabel satuanpendidikan
    $app->get('/satuanpendidikan', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL selectSatuanPendidikan');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    //get pada tabel kelas
    $app->get('/kelas', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL selectKelas');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    //get pada tabel data_isi_peserta_kelas
    $app->get('/data_isi_peserta_kelas', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL selectDataIsiPesertaKelas');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    // get by id_satuanpendidikan pada tabel satuan pendidikan
    $app->get('/satuanpendidikan/{id_satuanpendidikan}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL SelectSatuanPendidikanById(:id_satpen)');
        $query->bindParam(':id_satpen', $args['id_satuanpendidikan'], PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    // get by id_kelas pada tabel kelas
    $app->get('/kelas/{id_kelas}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL SelectKelasById(:kelas_id)');
        $query->bindParam(':kelas_id', $args['id_kelas'], PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    // get by id pada data_isi_peserta_kelas
    $app->get('/data_isi_peserta_kelas/{id}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL SelectDataIsiPesertaKelasById(:datakelas_id)');
        $query->bindParam(':datakelas_id', $args['id'], PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    // post data pada tabel kelas
    $app->post('/kelas', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();
    
        if (isset($parsedBody["id_kelas"]) && isset($parsedBody["nama_kelas"]) && isset($parsedBody["id_satuanpendidikan"])) {
            $id_kelas = $parsedBody["id_kelas"];
            $nama_kelas = $parsedBody["nama_kelas"];
            $id_satuanpendidikan = $parsedBody["id_satuanpendidikan"];
    
            $db = $this->get(PDO::class);
    
            try {
                $query = $db->prepare('CALL InsertKelas (?, ?, ?)');
                $query->execute([$id_kelas, $nama_kelas, $id_satuanpendidikan]);
    
                $responseData = [
                    'message' => 'Data yang dimasukkan pada tabel kelas berhasil disimpan'
                ];
    
                $response->getBody()->write(json_encode($responseData));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } catch (\Exception $e) {
                $responseData = [
                    'error' => 'Gagal menyimpan data kelas'
                ];
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
            
        } else {
            $responseData = [
                'error' => 'Data yang diperlukan tidak lengkap'
            ];
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    });

    //post data pada tabel satuanpendidikan
    $app->post('/satuanpendidikan', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
    
        $id_satuanpendidikan = $parsedBody["id_satuanpendidikan"];
        $nama_satuanpendidikan = $parsedBody["nama_satuanpendidikan"];
       
    
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL InsertSatuanPendidikan(?, ?)');
            $query->execute([$id_satuanpendidikan, $nama_satuanpendidikan]);
    
            $responseData = [
                'message' => 'Data Sekolah Berhasil disimpan.'
            ];
    
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $responseData = [
                'error' => 'Gagal Menyimpan Data Sekolah.'
            ];
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    // put data
    $app->put('/countries/{id}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();

        $currentId = $args['id'];
        $countryName = $parsedBody["name"];
        $db = $this->get(PDO::class);

        $query = $db->prepare('UPDATE countries SET name = ? WHERE id = ?');
        $query->execute([$countryName, $currentId]);

        $response->getBody()->write(json_encode(
            [
                'message' => 'country dengan id ' . $currentId . ' telah diupdate dengan nama ' . $countryName
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

    // delete data
    $app->delete('/countries/{id}', function (Request $request, Response $response, $args) {
        $currentId = $args['id'];
        $db = $this->get(PDO::class);

        try {
            $query = $db->prepare('DELETE FROM countries WHERE id = ?');
            $query->execute([$currentId]);

            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Data tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'country dengan id ' . $currentId . ' dihapus dari database'
                    ]
                ));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Database error ' . $e->getMessage()
                ]
            ));
        }

        return $response->withHeader("Content-Type", "application/json");
    });
};
