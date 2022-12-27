<?php

    abstract class ApiRoute extends DatabaseImplementedObject
    {
        abstract public function matchesPath(string $path): bool;

        abstract public function render(): array;
    }
