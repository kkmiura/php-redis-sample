<?php
# https://www.php.net/manual/ja/class.sessionhandlerinterface.php

interface SessionHandlerInterface {

    /* メソッド */
    public close(): bool
    public destroy(string $id): bool
    public gc(int $max_lifetime): int|false
    public open(string $path, string $name): bool
    public read(string $id): string|false
    public write(string $id, string $data): bool
}