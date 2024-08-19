<?php

if (file_exists(
    dirname(
        __DIR__
    ).'/var/cache/prod/App_Shared_Infrastructure_KernelProdContainer.preload.php'
)) {
    require dirname(
            __DIR__
        ).'/var/cache/prod/App_Shared_Infrastructure_KernelProdContainer.preload.php';
}
