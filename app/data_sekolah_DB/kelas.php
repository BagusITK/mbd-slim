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
    $app->get('/kelas', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL selectKelas');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    //get by id
    $app->get('/kelas/{id_kelas}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL SelectKelasById(:kelas_id)');
        $query->bindParam(':kelas_id', $args['id_kelas'], PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    //post
    $app->post('/kelas', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
    
        $nama_kelas = $parsedBody["nama_kelas"];
        $id_satuanpendidikan = $parsedBody["id_satuanpendidikan"];
       
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL InsertKelas(?, ?)');
            $query->execute([$nama_kelas, $id_satuanpendidikan]);
    
            $responseData = [
                'message' => 'Data Kelas Berhasil disimpan.'
            ];
    
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $responseData = [
                'error' => 'Gagal Menyimpan Data Kelas.'
            ];
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    //put
    $app->put('/kelas/{id_kelas}', function (Request $request, Response $response, $args) {
        $kelas_id = $args['id_kelas'];
        $data = $request->getParsedBody();
    
        $nama_kelas = $data['nama_kelas'];
        $id_satuanpendidikan = $data['id_satuanpendidikan'];
    
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL UpdateKelas(?, ?, ?)');
            $query->execute([$kelas_id, $nama_kelas, $id_satuanpendidikan]);
    
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode([
                    'message' => 'Data tidak ditemukan pada database'
                ]));
            } else {
                $response->getBody()->write(json_encode([
                    'message' => 'Data kelas dengan ID ' . $kelas_id . ' telah diperbarui.'
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
    $app->delete('/kelas', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL DeleteKelas()');
            $query->execute();
    
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Seluruh data yang ada pada tabel kelas telah dihapus'
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
    $app->delete('/kelas/{id_kelas}', function (Request $request, Response $response, $args) {
        $kelas_id = $args['id_kelas'];
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL DeleteKelasById(?)');
            $query->execute([$kelas_id]);
    
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
                        'message' => 'Data kelas dengan ID ' . $kelas_id . ' telah dihapus pada database '
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