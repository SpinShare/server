<?php

namespace SpinShare\SpeenOrbitalOS\Commands;

class ArchiveCommand extends Command
{
    public function getName()
    {
        return "archive";
    }

    public function getDescription()
    {
        return "Access scientific research reports";
    }

    public function getUsage()
    {
        return "archive [page]";
    }

    public function execute(array $args, bool $isAuthorized)
    {
        $page = $args[0] ?? "1";

        switch ($page) {
            case "1":
                return $this->getPage1();
            case "2":
                return $this->getPage2();
            case "3":
                return $this->getPage3();
            default:
                return "§F55Error: Page $page not found in archives.";
        }
    }

    private function getPage1()
    {
        $output = "§fff--- ARCHIVE: RESEARCH REPORT PSR J1719-1438 (Page 1/3) ---\n";
        $output .= "§fffSubject: PSR J1719-1438 Discovery and Classification\n";
        $output .= "§fffAuthor: star_blazer\n\n";
        $output .= "§666PSR J1719-1438 is a millisecond pulsar located approximately 4,000\n";
        $output .= "§666light-years from Earth in the constellation of Serpens Cauda. It was\n";
        $output .= "§666discovered in 2011 during a targeted search for pulsars. It has a rotation\n";
        $output .= "§666period of just 5.8 milliseconds, making it one of the fastest spinning\n";
        $output .= "§666objects known to science.\n\n \n";
        $output .= "§666What makes this system truly remarkable is not just the pulsar itself, but\n";
        $output .= "§666its companion. Initial orbital analysis suggests the presence of a\n";
        $output .= "§666low-mass object, likely the remains of a once-massive star.\n\n \n";
        $output .= "§fffUse 'archive 2' to read the next page.";
        return $output;
    }

    private function getPage2()
    {
        $output = "§fff--- ARCHIVE: RESEARCH REPORT PSR J1719-1438 (Page 2/3) ---\n";
        $output .= "§fffSubject: The Companion Object - PSR J1719-1438 b\n";
        $output .= "§fffAuthor: star_blazer\n\n";
        $output .= "§666The companion, PSR J1719-1438 b, is often referred to as the\n";
        $output .= "§666'Diamond Planet'. It orbits the pulsar at a distance of only 600,000\n";
        $output .= "§666kilometers with a period of 2.2 hours. Despite being roughly the same\n";
        $output .= "§666size as Jupiter, it has a mass slightly greater than Jupiter,\n";
        $output .= "§666indicating an extreme density.\n\n \n";
        $output .= "§666Given its origin as the core of a white dwarf that has been stripped\n";
        $output .= "§666of its outer layers by the pulsar's gravity, its composition is\n";
        $output .= "§666primarily carbon and oxygen. At such high pressures, the carbon must\n";
        $output .= "§666be crystalline. It is, quite literally, a planet-sized diamond.\n\n \n";
        $output .= "§fffUse 'archive 3' to read the final page.";
        return $output;
    }

    private function getPage3()
    {
        $output = "§fff--- ARCHIVE: RESEARCH REPORT PSR J1719-1438 (Page 3/3) ---\n";
        $output .= "§fffSubject: Personal Reflections on the 'Diamond in the Sky'\n";
        $output .= "§fffAuthor: star_blazer\n\n";
        $output .= "§666I often find myself staring at the data, mesmerized by the sheer\n";
        $output .= "§666scale of it. A diamond as large as a planet, forged in the death of\n";
        $output .= "§666a star and the birth of a pulsar. It's a reminder of the universe's\n";
        $output .= "§666capacity for creating beauty out of chaos.\n\n \n";
        $output .= "§666To think that something so precious and grand exists out there,\n";
        $output .= "§666spinning in the dark, silent and unreachable... it's unfathomable.\n";
        $output .= "§fff--- End of Report ---";
        return $output;
    }

    public function isAuthorizedOnly()
    {
        return true;
    }
}
