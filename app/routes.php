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

        $query = $db->prepare('SELECT * FROM satuanpendidikan WHERE id_satuanpendidikan=?');
        $query->execute([$args['id_satuanpendidikan']]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    // get by id_kelas pada tabel kelas
    $app->get('/kelas/{id_kelas}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('SELECT * FROM kelas WHERE id_kelas=?');
        $query->execute([$args['id_kelas']]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    // get by id pada data_isi_peserta_kelas
    $app->get('/data_isi_peserta_kelas/{id}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('SELECT * FROM data_isi_peserta_kelas WHERE id=?');
        $query->execute([$args['id']]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    // post data
    $app->post('/insertkelas', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();

        $db = $this->get(PDO::class);
        try {
        $query = $db->prepare('CALL InsertKelas (:kelas, :name, :id_satpen)');
        
        $query->bindParam(':kelas', $parsedBody['id_kelas'], PDO::PARAM_INT);
        $query->bindParam(':name', $parsedBody['nama_kelas'], PDO::PARAM_STR);
        $query->bindParam(':id_satpen', $parsedBody['id_satuanpendidikan'], PDO::PARAM_INT);
        $query->execute();
        $response->getBody()->write(json_encode(
            [
                'message' => 'Kelas ditambahkan pada id ' . ':idkelas'
            ]
        ));

        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode([
            'error' => 'Terjadi kesalahan dalam menambahkan kelas: ' 
        ]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
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
