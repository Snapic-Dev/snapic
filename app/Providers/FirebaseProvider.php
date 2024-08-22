<?php

namespace App\Providers;

class FirebaseProvider
{
    public function uploadToFirebase($file, $folder = 'images')
    {
        $firebaseConfig = [
            'apiKey' => "AIzaSyBCIPbd8ejdAoggzgLVJSGzPei_dKl479I",
            'storageBucket' => "urban-vogue-br.appspot.com",
        ];

        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $firebasePath = "{$folder}/{$fileName}";
        $url = "https://firebasestorage.googleapis.com/v0/b/{$firebaseConfig['storageBucket']}/o?name=" . urlencode($firebasePath);

        try {
            $fileContent = file_get_contents($file->getPathname());
            if ($fileContent === false) {
                throw new \Exception('Erro ao ler o conteúdo do arquivo.');
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/octet-stream',
                'Authorization: Bearer ' . $firebaseConfig['apiKey'],
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);

            $response = curl_exec($ch);
            if ($response === false) {
                throw new \Exception('Erro ao fazer upload no Firebase: ' . curl_error($ch));
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode !== 200) {
                throw new \Exception('Erro ao fazer upload no Firebase: ' . $response);
            }

            curl_close($ch);

            $downloadUrl = "https://firebasestorage.googleapis.com/v0/b/{$firebaseConfig['storageBucket']}/o/" . urlencode($firebasePath) . "?alt=media";
            return $downloadUrl;
        } catch (\Exception $e) {
            throw new \Exception('Erro ao fazer upload no Firebase: ' . $e->getMessage());
        }
    }
}
