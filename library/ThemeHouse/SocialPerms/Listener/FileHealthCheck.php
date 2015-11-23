<?php

class ThemeHouse_SocialPerms_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/ThemeHouse/SocialPerms/ControllerAdmin/SocialPermissionSet.php' => '3df4d5234a87c80c147ffd7a781873ad',
                'library/ThemeHouse/SocialPerms/DataWriter/SocialPermissionSet.php' => 'b3a116b34fdf1a0ad3902cf00e051e87',
                'library/ThemeHouse/SocialPerms/Extend/ThemeHouse/SocialGroups/ControllerAdmin/SocialCategory.php' => '39c2c432b089c68e36e479840af156ed',
                'library/ThemeHouse/SocialPerms/Extend/ThemeHouse/SocialGroups/ControllerPublic/SocialCategory.php' => '6f7c1797b135ae2869f73b667ae5a6dd',
                'library/ThemeHouse/SocialPerms/Extend/ThemeHouse/SocialGroups/ControllerPublic/SocialForum.php' => '22710549ac2b54a94e3a6979c0768802',
                'library/ThemeHouse/SocialPerms/Extend/ThemeHouse/SocialGroups/DataWriter/SocialForum.php' => 'cee52af441ca106671141702e461b529',
                'library/ThemeHouse/SocialPerms/Extend/ThemeHouse/SocialGroups/Model/SocialForum.php' => '0f1f64d31ccd927e37023ccc3f04c403',
                'library/ThemeHouse/SocialPerms/Extend/XenForo/DataWriter/Forum.php' => '46316e2fdf3ee965e3591eb5abd1bc73',
                'library/ThemeHouse/SocialPerms/Install/Controller.php' => '236251ba7926039b8915ce63c138fdc9',
                'library/ThemeHouse/SocialPerms/Listener/LoadClass.php' => '2c92825ac4571fc1abbc4389c464eb9d',
                'library/ThemeHouse/SocialPerms/Listener/TemplateCreate.php' => '4845f6893c854f377fd250684d3bf318',
                'library/ThemeHouse/SocialPerms/Listener/TemplateHook.php' => '8e1b9959a27451a770c7a500645229c0',
                'library/ThemeHouse/SocialPerms/Model/SocialPermissionSet.php' => '1fa8805f96dca111f85d890914c5a14e',
                'library/ThemeHouse/SocialPerms/Route/PrefixAdmin/SocialPermissionSets.php' => '060ca8b674dc5cf3abc37c73f280ce92',
                'library/ThemeHouse/SocialPerms/ViewAdmin/SocialPermissionSet/Export.php' => 'f71d20ad303b31ccc87728bbda77ac33',
                'library/ThemeHouse/Install.php' => '18f1441e00e3742460174ab197bec0b7',
                'library/ThemeHouse/Install/20151109.php' => '2e3f16d685652ea2fa82ba11b69204f4',
                'library/ThemeHouse/Deferred.php' => 'ebab3e432fe2f42520de0e36f7f45d88',
                'library/ThemeHouse/Deferred/20150106.php' => 'a311d9aa6f9a0412eeba878417ba7ede',
                'library/ThemeHouse/Listener/ControllerPreDispatch.php' => 'fdebb2d5347398d3974a6f27eb11a3cd',
                'library/ThemeHouse/Listener/ControllerPreDispatch/20150911.php' => 'f2aadc0bd188ad127e363f417b4d23a9',
                'library/ThemeHouse/Listener/InitDependencies.php' => '8f59aaa8ffe56231c4aa47cf2c65f2b0',
                'library/ThemeHouse/Listener/InitDependencies/20150212.php' => 'f04c9dc8fa289895c06c1bcba5d27293',
                'library/ThemeHouse/Listener/LoadClass.php' => '5cad77e1862641ddc2dd693b1aa68a50',
                'library/ThemeHouse/Listener/LoadClass/20150518.php' => 'f4d0d30ba5e5dc51cda07141c39939e3',
                'library/ThemeHouse/Listener/Template.php' => '0aa5e8aabb255d39cf01d671f9df0091',
                'library/ThemeHouse/Listener/Template/20150106.php' => '8d42b3b2d856af9e33b69a2ce1034442',
                'library/ThemeHouse/Listener/TemplateCreate.php' => '6bdeb679af2ea41579efde3e41e65cc7',
                'library/ThemeHouse/Listener/TemplateCreate/20150106.php' => 'c253a7a2d3a893525acf6070e9afe0dd',
                'library/ThemeHouse/Listener/TemplateHook.php' => 'a767a03baad0ca958d19577200262d50',
                'library/ThemeHouse/Listener/TemplateHook/20150106.php' => '71c539920a651eef3106e19504048756',
            ));
    }
}