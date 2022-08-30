<?php

namespace Shockedplot7560\MonologColored;

use Monolog\Level;
use Psr\Log\LogLevel;

class ColoredLevel {
    private static bool $initialized = false;

    private static array $color = [];
    private static string $reset = "";

    public static function init(): void{
        if(self::$initialized){
            return;
        }
        switch(OS::getOS()){
            case OS::OS_LINUX:
            case OS::OS_MACOS:
            case OS::OS_BSD:
                self::getEscapeCodes();
                return;

            case OS::OS_WINDOWS:
            case OS::OS_ANDROID:
                self::getFallbackEscapeCodes();
                return;
        }
    }

    public static function getTerminalColor(string|Level $level): string{
        if(!self::$initialized){
            self::init();
        }
        $level = $level instanceof Level ? $level->toPsrLogLevel() : $level;
        return self::$color[$level] ?? '';
    }

    public static function getReset(): string{
        if(!self::$initialized){
            self::init();
        }
        return self::$reset;
    }

    protected static function getFallbackEscapeCodes() : void{
        $color = fn(int $code) => "\x1b[38;5;${code}m";
        self::$reset = "\x1b[m";

        self::$color = [
            LogLevel::EMERGENCY => $color(203),
            LogLevel::ALERT => $color(203),
            LogLevel::CRITICAL => $color(203),
            LogLevel::ERROR => $color(124),
            LogLevel::WARNING => $color(227),
            LogLevel::NOTICE => $color(87),
            LogLevel::INFO => $color(231),
            LogLevel::DEBUG => $color(145)
        ];
    }

    protected static function getEscapeCodes() : void{
        $tput = fn(string $args) => is_string($result = shell_exec("tput $args")) ? $result : "";
        $setaf = fn(int $code) => $tput("setaf $code");

        self::$reset = $tput("sgr0");

        $colors = (int) $tput("colors");
        if($colors > 8){
            self::$color = [
                LogLevel::EMERGENCY => $colors >= 256 ? $setaf(203) : $setaf(9),
                LogLevel::ALERT => $colors >= 256 ? $setaf(203) : $setaf(9),
                LogLevel::CRITICAL => $colors >= 256 ? $setaf(203) : $setaf(9),
                LogLevel::ERROR => $colors >= 256 ? $setaf(124) : $setaf(1),
                LogLevel::WARNING => $colors >= 256 ? $setaf(227) : $setaf(11),
                LogLevel::NOTICE => $colors >= 256 ? $setaf(87) : $setaf(14),
                LogLevel::INFO => $colors >= 256 ? $setaf(231) : $setaf(15),
                LogLevel::DEBUG => $colors >= 256 ? $setaf(145) : $setaf(7)
            ];
        }else{
            self::$color = [
                LogLevel::EMERGENCY => $setaf(1),
                LogLevel::ALERT => $setaf(1),
                LogLevel::CRITICAL => $setaf(1),
                LogLevel::ERROR => $setaf(1),
                LogLevel::WARNING => $setaf(3),
                LogLevel::NOTICE => $setaf(6),
                LogLevel::INFO => $setaf(7),
                LogLevel::DEBUG => $setaf(7)
            ];
        }
    }
}