<?php

namespace Tests;

trait CreatesEnvFile {
    public function createEnvFile(string $fileName, array $contents): void {
        $this->removeEnvFile($fileName);

        $contentsString = '';
        foreach ($contents as $key => $value) {
            $contentsString .= "$key=$value\n";
        }

        file_put_contents($this->getEnvPath($fileName), $contentsString);
    }

    public function removeEnvFile(string $fileName): void {
        $path = $this->getEnvPath($fileName);

        if (file_exists($path)) {
            unlink($path);
        }
    }

    private function getEnvPath(string $fileName): string {
        return base_path($fileName);
    }
}
