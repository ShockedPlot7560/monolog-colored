<?php

namespace Shockedplot7560\MonologColored;

class OS{
    public const OS_WINDOWS = "win";
    public const OS_IOS = "ios";
    public const OS_MACOS = "mac";
    public const OS_ANDROID = "android";
    public const OS_LINUX = "linux";
    public const OS_BSD = "bsd";
    public const OS_UNKNOWN = "other";

    private static ?string $os = null;

    public static function getOS(bool $recalculate = false) : string{
        if(self::$os === null || $recalculate){
            $uname = php_uname("s");
            if(stripos($uname, "Darwin") !== false){
                if(strpos(php_uname("m"), "iP") === 0){
                    self::$os = self::OS_IOS;
                }else{
                    self::$os = self::OS_MACOS;
                }
            }elseif(stripos($uname, "Win") !== false || $uname === "Msys"){
                self::$os = self::OS_WINDOWS;
            }elseif(stripos($uname, "Linux") !== false){
                if(@file_exists("/system/build.prop")){
                    self::$os = self::OS_ANDROID;
                }else{
                    self::$os = self::OS_LINUX;
                }
            }elseif(stripos($uname, "BSD") !== false || $uname === "DragonFly"){
                self::$os = self::OS_BSD;
            }else{
                self::$os = self::OS_UNKNOWN;
            }
        }

        return self::$os;
    }
}