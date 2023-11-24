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
    $app->get('/siswa', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL selectAllSiswa');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    //get by id
    $app->get('/siswa/{id_siswa}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL SelectSiswaById(:siswa_id)');
        $query->bindParam(':siswa_id', $args['id_siswa'], PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    //post
    $app->post('/siswa', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
    
        $nama_siswa = $parsedBody["nama_siswa"];
        $nomor_induk = $parsedBody["nomor_induk"];
        $id_kelas = $parsedBody["id_kelas"];
       
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL InsertSiswa(?, ?, ?)');
            $query->execute([$nama_siswa, $nomor_induk, $id_kelas]);
    
            $responseData = [
                'message' => 'Data Siswa yang dimasukkan berhasil disimpan.'
            ];
    
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $responseData = [
                'error' => 'Gagal Menyimpan data siswa yang baru saja akan dimasukkan.'
            ];
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    //put
    $app->put('/siswa/{id_siswa}', function (Request $request, Response $response, $args) {
        $siswa_id = $args['id_siswa'];
        $data = $request->getParsedBody();
    
        $nama_siswa = $data['nama_siswa'];
        $nomor_induk = $data['nomor_induk'];
        $id_kelas = $data['id_kelas'];
    
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL UpdateSiswa(?, ?, ?, ?)');
            $query->execute([$siswa_id, $nama_siswa, $nomor_induk, $id_kelas]);
    
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode([
                    'message' => 'Data tidak ditemukan pada database'
                ]));
            } else {
                $response->getBody()->write(json_encode([
                    'message' => 'Data siswa dengan ID ' . $siswa_id . ' telah diperbarui.'
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
    $app->delete('/siswa', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL DeleteAllSiswa()');
            $query->execute();
    
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Seluruh data yang ada pada tabel siswa telah dihapus '
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
    $app->delete('/siswa/{id_siswa}', function (Request $request, Response $response, $args) {
        $siswa_id = $args['id_siswa'];
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