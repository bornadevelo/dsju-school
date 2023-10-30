<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit71fa358d2304315cb08082dbd6b5c25a
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $classMap = array (
        'Code_Snippets\\Active_Snippets' => __DIR__ . '/../..' . '/php/class-active-snippets.php',
        'Code_Snippets\\Admin' => __DIR__ . '/../..' . '/php/class-admin.php',
        'Code_Snippets\\Admin_Menu' => __DIR__ . '/../..' . '/php/admin-menus/class-admin-menu.php',
        'Code_Snippets\\Cloud\\Cloud_API' => __DIR__ . '/../..' . '/php/cloud/class-cloud-api.php',
        'Code_Snippets\\Cloud\\Cloud_Link' => __DIR__ . '/../..' . '/php/cloud/class-cloud-link.php',
        'Code_Snippets\\Cloud\\Cloud_Search_List_Table' => __DIR__ . '/../..' . '/php/cloud/class-cloud-search-list-table.php',
        'Code_Snippets\\Cloud\\Cloud_Snippet' => __DIR__ . '/../..' . '/php/cloud/class-cloud-snippet.php',
        'Code_Snippets\\Cloud\\Cloud_Snippets' => __DIR__ . '/../..' . '/php/cloud/class-cloud-snippets.php',
        'Code_Snippets\\Contextual_Help' => __DIR__ . '/../..' . '/php/class-contextual-help.php',
        'Code_Snippets\\DB' => __DIR__ . '/../..' . '/php/class-db.php',
        'Code_Snippets\\Data_Item' => __DIR__ . '/../..' . '/php/class-data-item.php',
        'Code_Snippets\\Edit_Menu' => __DIR__ . '/../..' . '/php/admin-menus/class-edit-menu.php',
        'Code_Snippets\\Export' => __DIR__ . '/../..' . '/php/export/class-export.php',
        'Code_Snippets\\Export_Attachment' => __DIR__ . '/../..' . '/php/export/class-export-attachment.php',
        'Code_Snippets\\Frontend' => __DIR__ . '/../..' . '/php/front-end/class-frontend.php',
        'Code_Snippets\\Import' => __DIR__ . '/../..' . '/php/export/class-import.php',
        'Code_Snippets\\Import_Menu' => __DIR__ . '/../..' . '/php/admin-menus/class-import-menu.php',
        'Code_Snippets\\List_Table' => __DIR__ . '/../..' . '/php/class-list-table.php',
        'Code_Snippets\\Manage_Menu' => __DIR__ . '/../..' . '/php/admin-menus/class-manage-menu.php',
        'Code_Snippets\\Plugin' => __DIR__ . '/../..' . '/php/class-plugin.php',
        'Code_Snippets\\REST_API\\Snippets_REST_Controller' => __DIR__ . '/../..' . '/php/rest-api/class-snippets-rest-controller.php',
        'Code_Snippets\\Settings\\Setting_Field' => __DIR__ . '/../..' . '/php/settings/class-setting-field.php',
        'Code_Snippets\\Settings_Menu' => __DIR__ . '/../..' . '/php/admin-menus/class-settings-menu.php',
        'Code_Snippets\\Snippet' => __DIR__ . '/../..' . '/php/class-snippet.php',
        'Code_Snippets\\Upgrade' => __DIR__ . '/../..' . '/php/class-upgrade.php',
        'Code_Snippets\\Validator' => __DIR__ . '/../..' . '/php/class-validator.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit71fa358d2304315cb08082dbd6b5c25a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit71fa358d2304315cb08082dbd6b5c25a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit71fa358d2304315cb08082dbd6b5c25a::$classMap;

        }, null, ClassLoader::class);
    }
}
