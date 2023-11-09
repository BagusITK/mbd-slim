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

     //post data pada tabel kelas
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

    //post data pada tabel satuanependidikan
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

    //post data pada tabel data_isi_peserta_kelas
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

    // put data pada tabel kelas
    $app->put('/kelas/{id_kelas}', function (Request $request, Response $response, $args) {
        $kelas_id = $args['id_kelas'];
        $data = $request->getParsedBody();
    
        $nama_kelas = $data['nama_kelas'];
        $id_satuanpendidikan = $data['id_satuanpendidikan'];
    
        $db = $this->get(PDO::class);
    
        try {
            $query = $db->prepare('CALL UpdateKelas(?, ?, ?)');
            $query->execute([$kelas_id, $nama_kelas]);
    
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

    // put data pada tabel data_isi_peserta_kelas
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

    // put data pada tabel satuanpendidikan
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

    // delete data pada tabel kelas
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

    // delete data pada tabel data_isi_peserta_kelas
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

    // delete data pada tabel satuanpendidikan
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

    //delete by id pada tabel kelas
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

    //delete by id pada tabel data_isi_peserta_kelas
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

    //delete by id pada tabel satuanpendidikan
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
