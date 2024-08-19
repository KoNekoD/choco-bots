<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Cast\RecastingRemovalRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php80\Rector\FunctionLike\MixedTypeRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Php82\Rector\Class_\ReadOnlyClassRector;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\CodeQuality\Rector\Class_\EventListenerToEventSubscriberRector;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\ArrowFunction\AddArrowFunctionReturnTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromReturnNewRector;
use Rector\TypeDeclaration\Rector\FunctionLike\AddParamTypeSplFixedArrayRector;

return static function (RectorConfig $rectorConfig): void {
    $dir = __DIR__.'/../../';
    $rectorConfig->paths([
        $dir.'/config',
        $dir.'/public',
        $dir.'/src',
        $dir.'/tests',
    ]);

    $rectorConfig->skip([
        AddArrowFunctionReturnTypeRector::class,
        ClosureToArrowFunctionRector::class,
        ReturnTypeFromReturnNewRector::class,

        // src/Shared issues
        EventListenerToEventSubscriberRector::class,
        RenameClassRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,

        // src/Main issues
        MixedTypeRector::class,
        AddParamTypeSplFixedArrayRector::class,
        RemoveUselessParamTagRector::class,
        RecastingRemovalRector::class,
        JsonThrowOnErrorRector::class,
        InlineConstructorDefaultToPropertyRector::class,
    ]);

    $rectorConfig->symfonyContainerXml(
        $dir.'/var/cache/dev/App_Shared_Infrastructure_KernelDevDebugContainer.xml'
    );

    // register rules
    $rectorConfig->rules([
        FinalizeClassesWithoutChildrenRector::class,
        ReadOnlyPropertyRector::class,
        ReadOnlyClassRector::class,
    ]);

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        SymfonySetList::SYMFONY_63,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        SetList::DEAD_CODE,
        SetList::CODE_QUALITY,
        SetList::TYPE_DECLARATION,
    ]);
};
