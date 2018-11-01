# php-awr-class : advance quran tajwid processor
awr is an advance wordprocessor/rulemaking for the Arabic language especially used for apply/showing Quran tajwid rules in real-time
online demo :


[rokhan](http://rokhan.ir)

[c# version](https://github.com/pfndesign/csharp-awr-class)

## how is this works :

with this class, you can make rules based on characters and their position to each other, tag them an show them with different colors
## usage :
```php
    $verse = "quran text";
    $verse2 = new awr_process($verse);
    $verse2->register_filter("filter_qalqala");
    $verse2->register_filter("filter_ghunna");
    $verse2->register_filter("filter_lqlab");
    $verse2->register_filter("filter_ikhfaa");
    $verse2->register_filter("filter_idgham");
    $verse2->register_filter("filter_idgham_without_ghunna");
    $verse2->register_filter("filter_maddah");
    $verse2->process();
    $verse2->reorder();
    $verse2->render();
```
## how to use :
### setup :
```php
$awr = new awr_process($text);
```
### register rules :
there are 7 rules that I created as part of tajwid rules

1. filter_qalqala
2. filter_ghunna
3. filter_lqlab
4. filter_ikhfaa
5. filter_idgham
6. filter_idgham_without_ghunna
7. filter_maddah

in order for filters/rules to work they must be registered
you can register filters/rules with 
```php
$awr->register_filter("filter name");
```
for example
```php
$awr->register_filter("filter_qalqala");
```
### finishing up : 
in order to fillters to work you have to call process() function . it will run every filter for every charecter in the text
```php
$awr->process();
```
after that reorder() must be called to restore characters original forms
```php
$awr->reorder();
```
### rendering : 

to render the final results you have to call render() function . it will echo processed character in <n> tag
  
```php
$awr->render();
```
render has 2 parameters

```php
$awr->render($tag,$return);
```

$tag can be used to change the "n" in <n> tag

$return can be used to switch between echo and return

## creating a custom rule : 

```php

private function wordrule_applyer($wordkey, $key, $tag, $words, $attachedby=false, $followedby=false, $followedbyattach=false, $lastlettercheck=false, $erab_flag=false)
```

all the filters in the class are using  wordrule_applyer to set a custom rule to check in the word. 

- $wordkey and $key are the positions of the character in the word. in the filters, we can use $key1 and $key2 as $wordkey and $key for wordrule_applyer function.

- $tag. if the function finds the rule it will set the tag to all the characters that applying to the rule. the tag will be used as a class name in HTML tag to display color for the rule.

- $words is an array of the characters Unicode that we are looking for in the word.

- $attachedby is an optional parameter. like $words, it accepts an array of the characters Unicode that is supposed to be attached to the characters in $words parameters without any space or word between them. you can also use a single value array with "erab" and "!erab" to check for the Arabic Irab.

- $followedby is an optional parameter. like $words, it accepts an array of the characters Unicode that supposed to be immediately after the $attachedby character but this time one or two erab can exist between them 

- $followedbyattach
is an optional parameter. like $words it accepts an array of the characters Unicode that supposed to be  immediately after the $followedby character but like $followedby one or two erab can be between them 

- $lastlettercheck is an optional parameter. if set true it will check if the previous sequence of characters is applied to a character at end of the word or not.

- $erab_flag  is an optional parameter. if set true it will include the word "alef" as an erab for rule accurately.

the function will return true if it finds the rule in the current position.

after understanding the wordrule_applyer you can simply create your Arabic rules

let's take look at some of the filters in the class :

```php

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
    
```
this is qalqala filter . in this filter we are looking for 

- $rule1 : "$charecters" that are attached by "$sukun"

- $rule2 : "$charecters" that are at the "end of the word" also we are accepting "alef" as Irab.

```php

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
    
```
or in the lqlab filter 
- $rule1 we are looking for a list of Arabic Irabs "$erabha" that are attached by "$b" ( "ب" )
- $rule2 is like the previous rule but with one level we are accepting additional Irabs between "$erabha" and "$b"
- $rule3 we are looking for the "$n" ( "ن" ) that are attached by "$sukon" ( special Arabic irab ) and are followed by "$b" 
- $rule4 is like the previous rule but we don't check for "$sukon" because sukon basically means a word without irab so if we check for the $n and $b that there is nothing between them is like checking for $sukon in rule3.

