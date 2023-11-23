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
    $app->get('/data_isi_peserta_kelas', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL selectDataIsiPesertaKelas');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    //get by id
    $app->get('/data_isi_peserta_kelas/{id}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL SelectDataIsiPesertaKelasById(:datakelas_id)');
        $query->bindParam(':datakelas_id', $args['id'], PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    //post
    $app->post('/data_isi_peserta_kelas', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
    
        $jumlah_rombel = $parsedBody["jumlah_rombel"];
        $peserta_didik = $parsedBody["peserta_didik"];
        $id_kelas = $parsedBody["id_kelas"];
       
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL InsertDataKelas(?, ?, ?)');
            $query->execute([$jumlah_rombel, $peserta_didik, $id_kelas]);
    
            $responseData = [
                'message' => 'Data isi peserta didik Berhasil disimpan.'
            ];
    
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $responseData = [
                'error' => 'Gagal Menyimpan Data isi peserta didik.'
            ];
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    //put
    $app->put('/data_isi_peserta_kelas/{id}', function (Request $request, Response $response, $args) {
        $datakelas_id = $args['id'];
        $data = $request->getParsedBody();
    
        $jumlah_rombel = $data['jumlah_rombel'];
        $peserta_didik = $data['peserta_didik'];
        $id_kelas = $data['id_kelas'];
    
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL UpdateDataIsiPesertaKelas(?, ?, ?, ?)');
            $query->execute([$datakelas_id, $jumlah_rombel, $peserta_didik, $id_kelas]);
    
            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode([
                    'message' => 'Data tidak ditemukan pada database'
                ]));
            } else {
                $response->getBody()->write(json_encode([
                    'message' => 'Data isi peserta kelas dengan ID ' . $datakelas_id . ' telah diperbarui.'
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
    $app->delete('/data_isi_peserta_kelas', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL DeleteDataIsiKelas()');
            $query->execute();
    
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Seluruh data yang ada pada tabel data_isi_peserta_kelas telah dihapus '
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
    $app->delete('/data_isi_peserta_kelas/{id}', function (Request $request, Response $response, $args) {
        $datakelas_id = $args['id'];
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL DeleteDataIsiKelasById(?)');
            $query->execute([$datakelas_id]);
    
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
                        'message' => 'Data isi kelas dengan ID ' . $datakelas_id . ' telah dihapus pada database '
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