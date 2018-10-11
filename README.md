# php-awr-class
php awr class :  advance arabic word and quran tajwid processor
awr is an advance wordprocessor/rulemaking for the Arabic language especially used for apply/showing Quran tajwid rules in real-time
online demo :
[rokhan](http://rokhan.ir)

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

## making custom rules :

you can use "wordrule_applyer" function in order to make a custom rules

```php
wordrule_applyer($wordkey, $key, $tag, $words, $attachedby=false, $followedby=false, $followedbyattach=false, $lastlettercheck=false, $erab_flag=false)
```
### what is wordrule_applyer:

wordrule_applyer is a function that used to make custom logical rules based on character positions to each other

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


