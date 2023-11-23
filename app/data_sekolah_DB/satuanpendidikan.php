<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    //get
    $app->get('/satuanpendidikan', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL selectSatuanPendidikan');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    //get by id
    $app->get('/satuanpendidikan/{id_satuanpendidikan}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL SelectSatuanPendidikanById(:id_satpen)');
        $query->bindParam(':id_satpen', $args['id_satuanpendidikan'], PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    //post
    $app->post('/satuanpendidikan', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
    
        $nama_satuanpendidikan = $parsedBody["nama_satuanpendidikan"];
       
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL InsertSatuanPendidikan(?)');
            $query->execute([$nama_satuanpendidikan]);
    
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

    //put
    $app->put('/satuanpendidikan/{id_satuanpendidikan}', function (Request $request, Response $response, $args) {
        $id_satpen = $args['id_satuanpendidikan'];
        $data = $request->getParsedBody();
    
        $nama_satuanpendidikan = $data['nama_satuanpendidikan'];
    
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL UpdateSatuanPendidikan(?, ?)');
            $query->execute([$id_satpen, $nama_satuanpendidikan]);
    
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode([
                    'message' => 'Data tidak ditemukan pada database'
                ]));
            } else {
                $response->getBody()->write(json_encode([
                    'message' => 'Data kelas dengan ID ' . $id_satpen . ' telah diperbarui.'
                ]));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode([
                'message' => 'Terdapat error pada database ' . $e->getMessage()
            ]));
        }
    
        return $response->withHeader("Content-Type", "application/json");
    });

    //delete
    $app->delete('/satuanpendidikan', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL DeleteSatuanPendidikan()');
            $query->execute();
    
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Seluruh data yang ada pada tabel satuanpendidikan telah dihapus'
                ]
            ));
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Terdapat error pada database ' . $e->getMessage()
                ]
            ));
        }
    
        return $response->withHeader("Content-Type", "application/json");
    });

    //delete by id
    $app->delete('/satuanpendidikan/{id_satuanpendidikan}', function (Request $request, Response $response, $args) {
        $id_satpen = $args['id_satuanpendidikan'];
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL DeleteSatuanPendidikanById(?)');
            $query->execute([$id_satpen]);
    
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
                        'message' => 'Data isi kelas dengan ID ' . $id_satpen . ' telah dihapus pada database '
                    ]
                ));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Terdapat error pada database ' . $e->getMessage()
                ]
            ));
        }
    
        return $response->withHeader("Content-Type", "application/json");
    });
};