<?php
require_once('persian_log2vis-2.0/persian_log2vis.php');

/**
 * [awr_process description]
 * ver 2.0
 * awr_process is a advanced word rule processor especially for arabic/quran
 * author  : peyman farahmand
 * email : pfndesigen@gmail.com
 * date : 11/10/2018
 */

/**
 * [testing description]
 * fully checked surah list :
 * an-nas
 * al-falaq
 * al-ikhlas
 * al-masadd
 * al-nasr
 * al-kafiroon
 * al-kauther
 * al-maun
 * al-quraish
 * al-fil
 * al-humaza
 * al-asr
 * al-takathur
 * al-qaria
 * al-adiyat
 * al-zalzala
 * al-bayyina
 * al-qadr
 * al-alaq
 * at-tin
 * al-inshirah
 * at this point, I'm certain that everything is working
 * reminder: for tajvid rules to work correctly all words must have a correct Arabic erab.
 */

class awr_process
{
    public $text;
    private $filters=array();

    public function __construct($string)
    {
        $this->text=$string;
        $this->ready();
    }
    /**
     * [ready description]
     * splitting text to words and words to characters
     * converting each character to Unicode character to keeping there original form in the word
     * @return [type] array [description] unicode characters from the word
     */
    private function ready()
    {
        $verse = array_filter(explode(" ", $this->text));
        foreach ($verse as $key => $matn) {
            $verse[$key] = array_reverse(array_filter(persian_log2vis($matn)));
        }
        $this->text = array_filter($verse);
        return $this->text;
    }
    /**
     * [register_filter description]
     * register filters/rules to the process function
     * default rule functions :
     * filter_qalqala
     * filter_ghunna
     * filter_lqlab
     * filter_ikhfaa
     * filter_idgham
     * filter_idgham_without_ghunna
     * filter_maddah
     * @param  [type] $function_name [description] name of the rule functions
     */
    public function register_filter($function_name)
    {
        if (!in_array($function_name, $this->filters) && method_exists($this, $function_name)) {
            $this->filters[]=$function_name;
        }
    }
    /**
     * [process description]
     * calls each registered filter/rule function to process each character
     */
    public function process()
    {
        foreach ($this->text as $key1 => $parts) {
            foreach ($parts as $key2 => $char) {
                foreach ($this->filters as $function_name) {
                    call_user_func_array(array($this, $function_name), array($key1,$key2));
                }
            }
        }
    }
    /**
     * [set_word_flag description]
     * set the flag for the charecters
     * @param [type] $key1 [description] the position of the character in array / row
     * @param [type] $key2 [description] the position of the character in array / column
     * @param [type] $flag [description] rule flag name
     */
    private function set_word_flag($key1, $key2, $flag)
    {
        $this->text[$key1][$key2]['flag'] = $flag;
    }
    /**
     * [wordrule_applyer description]
     * applying/checking the word rules for characters
     * @param  [type]  $wordkey          [description] the position of the character in array / row
     * @param  [type]  $key              [description] the position of the character in array / column
     * @param  [type]  $tag              [description] rule flag name
     * @param  [type]  $words            [description] array of charecters
     * @param  boolean $attachedby       [description] false or array of characters that must be attached to $words
     * @param  boolean $followedby       [description] false or array of characters that must be followed by $words / $attachedby if not false
     * @param  boolean $followedbyattach [description] false or array of characters that must be attached to $followedby if not false
     * @param  boolean $lastlettercheck  [description] false or check if the previous rules are for a character at end of the text
     * @param  boolean $erab_flag        [description] true if you don't want to use alef as erab
     * @return [type]                    [description] if the rule applies for character
     */
    private function wordrule_applyer($wordkey, $key, $tag, $words, $attachedby=false, $followedby=false, $followedbyattach=false, $lastlettercheck=false, $erab_flag=false)
    {
        $path=array();
        $wordkey1=$wordkey2=$wordkey3=$wordkey4=$wordkey;
        $key1=$key+1;
        $key2=$key+2;
        $key3=$key+3;
        $key4=$key+4;
        $index=0;
        $sourcetext = str_replace('"', "", json_encode($this->text[$wordkey][$key]['char']));
        if (isset($this->text[$wordkey1][$key1])) {
            $sourcetext_plus = str_replace('"', "", json_encode($this->text[$wordkey1][$key1]['char']));
        } elseif (isset($this->text[$wordkey1+1][$index])) {
            $wordkey1=$wordkey1+1;
            $key1 = $index;
            $index++;
            $sourcetext_plus = str_replace('"', "", json_encode($this->text[$wordkey1][$key1]['char']));
        }

        if (isset($this->text[$wordkey2][$key2])) {
            $sourcetext_plustwo = str_replace('"', "", json_encode($this->text[$wordkey2][$key2]['char']));
        } elseif (isset($this->text[$wordkey2+1][$index])) {
            $wordkey2=$wordkey2+1;
            $key2 = $index;
            $index++;
            $sourcetext_plustwo = str_replace('"', "", json_encode($this->text[$wordkey2][$key2]['char']));
        }

        if (isset($this->text[$wordkey3][$key3])) {
            $sourcetext_plusthree = str_replace('"', "", json_encode($this->text[$wordkey3][$key3]['char']));
        } elseif (isset($this->text[$wordkey3+1][$index])) {
            $wordkey3=$wordkey3+1;
            $key3 = $index;
            $index++;
            $sourcetext_plusthree = str_replace('"', "", json_encode($this->text[$wordkey3][$key3]['char']));
        }
        if (isset($this->text[$wordkey4][$key4])) {
            $sourcetext_plusfour = str_replace('"', "", json_encode($this->text[$wordkey4][$key4]['char']));
        } elseif (isset($this->text[$wordkey4+1][$index])) {
            $wordkey4=$wordkey4+1;
            $key4 = $index;
            $index++;
            $sourcetext_plusfour = str_replace('"', "", json_encode($this->text[$wordkey4][$key4]['char']));
        }
        if (!in_array($sourcetext, $words)) {
            return false;
        }
        /**
         * [$path description]
         * save path of characters that applied to the rule
         * @var array
         */
        $path=array();
        /**
         * [if description]
         * check $attachedby
         * $attachedby can have a array of characters or especially "erab" or "!erab" to check for earb!! duh
         */
        if ($attachedby && isset($this->text[$wordkey1][$key1])) {
            if (in_array("erab", $attachedby) && !$this->erab($this->text[$wordkey1][$key1]['char'], $erab_flag)) {
                return false;
            } elseif (in_array("erab", $attachedby) && $this->erab($this->text[$wordkey1][$key1]['char'], $erab_flag)) {
                $path[]=array($wordkey1,$key1);
            } elseif (in_array("!erab", $attachedby) && $this->erab($this->text[$wordkey1][$key1]['char'], $erab_flag)) {
                return false;
            } elseif (in_array("!erab", $attachedby) && !$this->erab($this->text[$wordkey1][$key1]['char'], $erab_flag)) {
                $path[]=array($wordkey1,$key1);
            }
            if (is_array($attachedby) && ((!in_array("erab", $attachedby) && !in_array("!erab", $attachedby)) || count($attachedby)>1) && !in_array($sourcetext_plus, $attachedby)) {
                return false;
            } elseif (is_array($attachedby) && ((!in_array("erab", $attachedby) && !in_array("!erab", $attachedby)) || count($attachedby)>1) && in_array($sourcetext_plus, $attachedby)) {
                $path[]=array($wordkey1,$key1);
            }
        } elseif ($attachedby && !isset($this->text[$wordkey1][$key1])) {
            return false;
        }
        /**
         * [if description]
         * check $followedby
         * $followedby can have a array of characters
         */
        if ($followedby && (isset($this->text[$wordkey2][$key2]) || isset($this->text[$wordkey3][$key3]))) {
            if (is_array($followedby)) {
                if (isset($this->text[$wordkey2][$key2]) && !isset($this->text[$wordkey3][$key3]) && !in_array($sourcetext_plustwo, $followedby) || (isset($this->text[$wordkey2][$key2]) && isset($this->text[$wordkey3][$key3]) && !$this->erab($this->text[$wordkey2][$key2]['char'], $erab_flag) && !in_array($sourcetext_plustwo, $followedby)) || (isset($this->text[$wordkey2][$key2]) && isset($this->text[$wordkey3][$key3]) && $this->erab($this->text[$wordkey2][$key2]['char'], $erab_flag) && !in_array($sourcetext_plusthree, $followedby))) {
                    return false;
                } elseif (isset($this->text[$wordkey2][$key2]) && isset($this->text[$wordkey3][$key3]) && $this->erab($this->text[$wordkey2][$key2]['char'], $erab_flag) && in_array($sourcetext_plusthree, $followedby)) {
                    $path[]=array($wordkey1,$key1);
                    $path[]=array($wordkey2,$key2);
                    $path[]=array($wordkey3,$key3);
                } elseif (isset($this->text[$wordkey2][$key2]) && in_array($sourcetext_plustwo, $followedby)) {
                    $path[]=array($wordkey1,$key1);
                    $path[]=array($wordkey2,$key2);
                }
            }
        } elseif ($followedby && !isset($this->text[$wordkey2][$key2])) {
            return false;
        }
        /**
         * [if description]
         * check $followedbyattach if $followedby is true
         * $followedbyattach can have a array of characters
         */
        if ($followedby && $followedbyattach && (isset($this->text[$wordkey3][$key3]) || isset($this->text[$wordkey4][$key4]))) {
            if (is_array($followedbyattach)) {
                if (isset($this->text[$wordkey3][$key3]) && !isset($this->text[$wordkey4][$key4]) && !in_array($sourcetext_plusthree, $followedbyattach) || (isset($this->text[$wordkey3][$key3]) && isset($this->text[$wordkey4][$key4]) && !$this->erab($this->text[$wordkey3][$key3]['char'], $erab_flag) && !in_array($sourcetext_plusthree, $followedbyattach)) || (isset($this->text[$wordkey3][$key3]) && isset($this->text[$wordkey4][$key4]) && $this->erab($this->text[$wordkey3][$key3]['char'], $erab_flag) && !in_array($sourcetext_plusfour, $followedbyattach))) {
                    return false;
                } elseif (isset($this->text[$wordkey3][$key3]) && isset($this->text[$wordkey4][$key4]) && $this->erab($this->text[$wordkey3][$key3]['char'], $erab_flag) && in_array($sourcetext_plusfour, $followedbyattach)) {
                    $path[]=array($wordkey3,$key3);
                    $path[]=array($wordkey4,$key4);
                } elseif (isset($this->text[$wordkey3][$key3]) && in_array($sourcetext_plusthree, $followedbyattach)) {
                    $path[]=array($wordkey3,$key3);
                }
            }
        } elseif ($followedby && $followedbyattach && !isset($this->text[$wordkey3][$key3])) {
            return false;
        }

        /**
         * [if description]
         * check if the character is at end of the text
         */
        if ($lastlettercheck && (isset($this->text[$wordkey+1]) || (isset($this->text[$wordkey1][$key1]) && !isset($this->text[$wordkey2][$key2]) && $this->erab($this->text[$wordkey1][$key1]['char'], $erab_flag) && count($this->text[$wordkey1])-1!=$key1) || (isset($this->text[$wordkey1][$key1]) && isset($this->text[$wordkey2][$key2]) && $this->erab($this->text[$wordkey1][$key1]['char'], $erab_flag) && $this->erab($this->text[$wordkey2][$key2]['char'], $erab_flag) && count($this->text[$wordkey1])-1!=$key2)  || (isset($this->text[$wordkey1][$key1]) && isset($this->text[$wordkey2][$key2]) && $this->erab($this->text[$wordkey1][$key1]['char'], $erab_flag) && !$this->erab($this->text[$wordkey2][$key2]['char'], $erab_flag)))) {
            return false;
        }

        $path[]=array($wordkey,$key);

        $paths = array_unique($path, SORT_REGULAR);
        /**
         * [foreach description]
         * applying the flag to the characters in $path
         */
        foreach ($paths as $wkey) {
            $this->set_word_flag($wkey[0], $wkey[1], $tag);
        }

        return true;
    }
    /**
     * [erab description]
     * check if the character is erab
     * @param  [type] $text      [description] character
     * @param  [type] $erab_flag [description] use without alef if true
     * @return [type] bool
     */
    public function erab($text, $erab_flag=false)
    {
        $erablist = array(
        "d98b",
        "d98c",
        "d98e",
        "d98f",
        "d990",
        "d991",
        "d992",
        "d993", //madhe
        "d994", //zameh
        "d996", //alefkochik
        "d997", //dammah
        "d998", //noon ghunna
        "d999", //zwarakay
        "d99a", //vowel
        "d99b", //vowel
        "d99d", //reversed damma
        "d9b0",
        "d98d",
        "efba8e", //alef
        "efba8d",
        "efbbbb", // la


    );
        if ($erab_flag) {
            $erablist = array_diff($erablist, ["efba8e", "efba8e"]);
        }

        $text   = bin2hex($text);

        if (in_array($text, $erablist)) {
            return true;
        }

        return false;
    }
    /**
     * [filter_qalqala description]
     * check tajvid qalqala rules
     * @param  [type] $key1 [description] the position of the character in array / row
     * @param  [type] $key2 [description] the position of the character in array / column
     * @return [type] bool
     */
    public function filter_qalqala($key1, $key2)
    {
        $charecters = array(
      '\u0642',
      '\ufed5',
      '\ufed6', //ق
      '\ufed7',
      '\ufed8',
      '\u0637',
      '\ufec1',
      '\ufec2', //ط
      '\ufec3',
      '\ufec4',
      '\u0628',
      '\ufe8f',
      '\ufe90', //ب
      '\ufe91',
      '\ufe92',
      '\u062c',
      '\ufe9d',
      '\ufe9e', //ج
      '\ufea0',
      '\ufe9f',
      '\u062f',
      '\ufea9', //د
      '\ufeaa'
  );
        $sukun      = array("\u0652");
        $rule1 = $this->wordrule_applyer($key1, $key2, "qalqala", $charecters, $sukun);
        $rule2 = $this->wordrule_applyer($key1, $key2, "qalqala", $charecters, false, false, false, true, true);
        /*
        some of Quran surah that I checked manually for this filter :
        falaq,ikhlas,masadd,nasr,kafiroon,kauther
         */
        return ($rule1 || $rule2);
    }
    /**
     * [filter_ghunna description]
     * check tajvid ghunna rules
     * @param  [type] $key1 [description] the position of the character in array / row
     * @param  [type] $key2 [description] the position of the character in array / column
     * @return [type] bool
     */
    public function filter_ghunna($key1, $key2)
    {
        $m      = array(
          '\u0645',
          '\ufee1',
          '\ufee2', //م
          '\ufee4',
          '\ufee3'
      );
        $n      = array(
          '\u0646',
          '\ufee5',
          '\ufee6', //ن
          '\ufee8',
          '\ufee7'
      );
        $tasdid = array("\u0651");
        $rule1 = $this->wordrule_applyer($key1, $key2, "chunna", $m, $tasdid, false, false, false, true);
        $rule2 = $this->wordrule_applyer($key1, $key2, "chunna", $n, $tasdid, false, false, false, true);
        /*
        some of Quran surah that I checked manually for this filter :
        nas,falaq,masadd,nasr,kauther
         */
        return ($rule1 || $rule2);
    }
    /**
     * [filter_lqlab description]
     * check tajvid lqlab rules
     * @param  [type] $key1 [description] the position of the character in array / row
     * @param  [type] $key2 [description] the position of the character in array / column
     * @return [type] bool
     */
    public function filter_lqlab($key1, $key2)
    {
        $erabha = array(
        '\u064b',
        '\u064d',
        '\u064c'
        );

        $n = array(
        '\u0646',
        '\ufee5',
        '\ufee6', //ن
        '\ufee8',
        '\ufee7'
    );

        $b = array(
        '\u0628',
        '\ufe8f',
        '\ufe90', //ب
        '\ufe91',
        '\ufe92'
    );
        $sukon = array("\u0652");

        $rule1 = $this->wordrule_applyer($key1, $key2, "lqlab", $erabha, $b);
        $rule2 = $this->wordrule_applyer($key1, $key2, "lqlab", $erabha, array("erab"), $b);
        $rule3 = $this->wordrule_applyer($key1, $key2, "lqlab", $n, $sukon, $b);
        $rule4 = $this->wordrule_applyer($key1, $key2, "lqlab", $n, $b);
        /*
        some of Quran surah that I checked manually for this filter :
        baqara:10,18,19,27,31,33
         */
        return ($rule1 || $rule2 || $rule3 || $rule4);
    }
    /**
     * [filter_ikhfaa description]
     * check tajvid ikhfaa rules
     * @param  [type] $key1 [description] the position of the character in array / row
     * @param  [type] $key2 [description] the position of the character in array / column
     * @return [type] bool
     */
    public function filter_ikhfaa($key1, $key2)
    {
        $theseletter = array(
        '\u062a',
        '\ufe95',
        '\ufe96', //ت
        '\ufe97',
        '\ufe98',
        '\ufe99',
        '\u062b',
        '\ufe99',
        '\ufe9a', //ث
        '\ufe9c',
        '\ufe9b',
        '\u062c',
        '\ufe9d',
        '\ufe9e', //ج
        '\ufea0',
        '\ufe9f',
        '\u062f',
        '\ufea9', //د
        '\ufeaa',
        '\u0630',
        '\ufeab', //ذ
        '\ufeac',
        '\u0632',
        '\ufeaf', //ز
        '\ufeb0',
        '\u0633',
        '\ufeb1',
        '\ufeb2', //س
        '\ufeb3',
        '\ufeb4',
        '\u0634',
        '\ufeb5',
        '\ufeb6', //ش
        '\ufeb7',
        '\ufeb8',
        '\u0635',
        '\ufeb9',
        '\ufeba', //ص
        '\ufebb',
        '\ufebc',
        '\u0636',
        '\ufebd',
        '\ufebe', //ض
        '\ufebf',
        '\ufec0',
        '\u0637',
        '\ufec1',
        '\ufec2', //ط
        '\ufec3',
        '\ufec4',
        '\u0638',
        '\ufec5',
        '\ufec6', //ط
        '\ufec7',
        '\ufec8',
        '\u0641',
        '\ufed1',
        '\ufed2', //ف
        '\ufed3',
        '\ufed4',
        '\u0642',
        '\ufed5',
        '\ufed6', //ق
        '\ufed7',
        '\ufed8',
        '\u0643',
        '\ufed9',
        '\ufeda', //ک
        '\ufedb',
        '\ufedc'
    );
        $erabha        = array(
        '\u064b',
        '\u064d',
        '\u064c'
    );
        $n       = array(
        '\u0646',
        '\ufee5',
        '\ufee6', // ن
        '\ufee8',
        '\ufee7'
    );
        $b = array(
        '\u0628',
        '\ufe8f',
        '\ufe90', //ب
        '\ufe91',
        '\ufe92'
     );
        $m      = array(
        '\u0645',
        '\ufee1',
        '\ufee2', //م
        '\ufee4',
        '\ufee3'
     );
        $sukon = array("\u0652");
        $rule1 = $this->wordrule_applyer($key1, $key2, "ikhfaa", $erabha, $theseletter);
        $rule2 = $this->wordrule_applyer($key1, $key2, "ikhfaa", $n, $sukon, $theseletter);
        $rule3 = $this->wordrule_applyer($key1, $key2, "ikhfaa", $m, $sukon, $b);
        $rule4 = $this->wordrule_applyer($key1, $key2, "ikhfaa", $erabha, array("erab"), $theseletter);
        /*
        some of Quran surah that I checked manually for this filter :
        falaq,masadd:3,kafiroon,maun,quraish:4,fil:4,baqare:17,10
         */
        return ($rule1 && $rule2 && $rule3 && $rule4);
    }
    /**
     * [filter_idgham description]
     * check tajvid idgham rules
     * @param  [type] $key1 [description] the position of the character in array / row
     * @param  [type] $key2 [description] the position of the character in array / column
     * @return [type] bool
     */
    public function filter_idgham($key1, $key2)
    {
        $erabha        = array(
        '\u064b',
        '\u064d',
        '\u064c'
    );
        $n       = array(
        '\u0646',
        '\ufee5',
        '\ufee6',
        '\ufee8',
        '\ufee7'
    );
        $theseletter = array(
        '\u064a',
        '\ufef1',
        '\ufef2',
        '\ufef3',
        '\ufef4',
        '\u0649',
        '\ufeef',
        '\uFef0',
        '\u0646',
        '\ufee5',
        '\ufee6',
        '\ufee7',
        '\ufee8',
        '\u0645',
        '\ufee1',
        '\ufee2',
        '\ufee3',
        '\ufee4',
        '\u0648',
        '\ufeed',
        '\ufeee'
    );
        $m      = array(
        '\u0645',
        '\ufee1',
        '\ufee2', //م
        '\ufee4',
        '\ufee3'
      );
        $sukon = array("\u0652");
        $tasdid = array("\u0651");
        $rule1 = $this->wordrule_applyer($key1, $key2, "idgham", $erabha, $theseletter);
        $rule2 = $this->wordrule_applyer($key1, $key2, "idgham", $erabha, array("erab"), $theseletter);
        $rule3 = $this->wordrule_applyer($key1, $key2, "idgham", $n, $sukon, $theseletter);
        $rule4 = $this->wordrule_applyer($key1, $key2, "idgham", $n, $theseletter);
        $rule5 = $this->wordrule_applyer($key1, $key2, "idgham", $m, $sukon, $m, $tasdid);
        $rule6 = $this->wordrule_applyer($key1, $key2, "idgham", $m, $m, $tasdid);
        /*
        some of Quran surah that I checked manually for this filter :
        masadd,kafiroon:4,quraish:4,fil:4,5,humaza:2,zalzala:7,8
         */
        return ($rule1||$rule2||$rule3||$rule4||$rule5||$rule6);
    }
    /**
     * [filter_idgham_without_ghunna description]
     * check tajvid idgham without ghunna rules
     * @param  [type] $key1 [description] the position of the character in array / row
     * @param  [type] $key2 [description] the position of the character in array / column
     * @return [type] bool
     */
    public function filter_idgham_without_ghunna($key1, $key2)
    {
        $erabha        = array(
          '\u064b',
          '\u064d',
          '\u064c'
      );
        $n       = array(
          '\u0646',
          '\ufee5',
          '\ufee6', //ن
          '\ufee8',
          '\ufee7'
      );
        $theseletter = array(
          '\u0644',
          '\ufedd',
          '\ufede',
          '\ufedf',
          '\ufee0',
          '\u0631',
          '\ufead',
          '\ufeae'
      );
        $sukon = array("\u0652");
        $rule1 = $this->wordrule_applyer($key1, $key2, "idghamwg", $erabha, $theseletter);
        $rule2 = $this->wordrule_applyer($key1, $key2, "idghamwg", $erabha, array("erab"), $theseletter);
        $rule3 = $this->wordrule_applyer($key1, $key2, "idghamwg", $n, $sukon, $theseletter);
        $rule4 = $this->wordrule_applyer($key1, $key2, "idghamwg", $n, $theseletter);
        return ($rule1 || $rule2 || $rule3 || $rule4);
        /*
        some of Quran surah that I checked manually for this filter :
        ikhlas:4,maun:4,humaza:1
         */
    }
    /**
     * [filter_maddah description]
     * check maddah
     * @param  [type] $key1 [description] the position of the character in array / row
     * @param  [type] $key2 [description] the position of the character in array / column
     * @return [type] bool
     */
    public function filter_maddah($key1, $key2)
    {
        $maddah = array("\u0653");
        $rule1 = $this->wordrule_applyer($key1, $key2, "maddah", $maddah);
        return $rule1;
    }
    /**
     * [reorder description]
     * bascilly gluing words and flags to gather for render
     */
    public function reorder()
    {
        foreach ($this->text as $key1 => $parts) {
            for ($key2 = count($parts) - 1; $key2 >= 0; $key2--) {
                //fix deleting alef in first letter
               if ((key2==0 && $this->erab($this->text[$key1][$key2]['char'])) && $this->erab($this->text[$key1][$key2]['char'],true)) || (key2 != 0 && $this->erab($this->text[$key1][$key2]['char']))){
                    if (isset($this->text[$key1][$key2 - 1])) {
                        $this->text[$key1][$key2 - 1]['word'] = $this->text[$key1][$key2 - 1]['word'] . $this->text[$key1][$key2]['word'];
                    }
                    if ($this->text[$key1][$key2]['flag'] != "none" && $this->text[$key1][$key2 - 1]['flag'] == "none") {
                        $this->text[$key1][$key2 - 1]['flag'] = $this->text[$key1][$key2]['flag'];
                    }
                    unset($this->text[$key1][$key2]);
                }
            }
        }
    }
    /**
     * [render description]
     * render whole text with rules applied to them
     * @param  string $tag [description] tag name
     * @param  boolean $return [description] return of echo / default is echo
     * @return [type] html    [description] final text
     */
    public function render($tag="n", $return=false)
    {
        $final_text="";
        foreach ($this->text as $key1 => $parts) {
            foreach ($parts as $key2 => $char) {
                $final_text.="<".$tag." class=\"" . $char['flag'] . "\">" . $char['word'] . "</".$tag.">";
            }
            $final_text.="<".$tag."> </".$tag.">";
        }
        if ($return) {
            return $final_text;
        }

        echo $final_text;
    }
}
