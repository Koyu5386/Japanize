<?
namespace Japanize;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerChatEvent;

class Main extends PluginBase implements Listener{

  public function onLoad(){
	}

  public function onEnable(){
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }

  public function onDisable(){
	}

  public function Chat(PlayerChatEvent $e){
    $m = $e->getMessage();
    //マルチバイト文字が含まれていないか（＝平仮名などが含まれていないか）
    if(strlen($m) === mb_strlen($m)){
      //先頭が「＠」で始まっているか
      if(substr($m, 0, 1) === '@'){
        $name = $e->getPlayer()->getName();
        $m = ltrim($m, '@');
        $transfered = $this->transfer($m);
        Server::getInstance()->broadcastMessage('<'.$name.'> '.$transfered.' §o§7('.$m.')');
        $e->setCancelled(true);
      }
    }

  }

  public function transfer(string $m){
    $hiraganized = $this->hiraganize($m);

    $keyword = mb_convert_encoding($hiraganized,'UTF-8', 'auto');
    $text = urlencode($keyword);
    $langpair = urlencode('ja-Hira|ja');
    $url = 'http://www.google.com/transliterate?langpair='.$langpair.'&text='.$text;

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $post = curl_exec($curl);
    curl_close($curl);

    $arrays  = json_decode($post, true);
    $japanized = "";
    foreach ($arrays as $array) {
        $mes = $array[1][0];
        $japanized .= $mes;
    }

    return $japanized;
  }

  public function hiraganize(string $m){
    $r = array(
            'BB','CC','DD','FF','GG','HH','JJ','KK','LL','MM','NN','PP','QQ','RR','SS','TT','VV','WW','XX','YY','ZZ',
            'KA','KI','KU','KE','KO',
            'GA','GI','GU','GE','GO',
            'KYA','KYI','KYU','KYE','KYO',
            'GYA','GYI','GYU','GYE','GYO',
            'SHA','SHI','SHU','SHE','SHO',
            'TSU','SA','SHI','SU','SE','SO',
            'ZA','ZI','ZU','ZE','ZO',
            'SYA','SYI','SYU','SYE','SYO',
            'JA','JI','JU','JE','JO',
            'ZYA','ZYI','ZYU','ZYE','ZYO',
            'XTU','LTU','TA','TI','TU','TE','TO',
            'DYA','DYI','DYU','DYE','DYO',
            'DHA','DHI','DHU','DHE','DHO',
            'DA','DI','DU','DE','DO',
            'CHA','CHI','CHU','CHE','CHO',
            'TYA','TYI','TYU','TYE','TYO',
            'NA','NI','NU','NE','NO',
            'NYA','NYI','NYU','NYE','NYO',
            'THA','THI','THU','THE','THO',
            'HA','HI','HU','HE','HO',
            'BA','BI','BU','BE','BO',
            'HYA','HYI','HYU','HYE','HYO',
            'BYA','BYI','BYU','BYE','BYO',
            'PA','PI','PU','PE','PO',
            'PYA','PYI','PYU','PYE','PYO',
            'MA','MI','MU','ME','MO',
            'MYA','MYI','MYU','MYE','MYO',
            'RYA','RYI','RYU','RYE','RYO',
            'YA','YI','YU','YE','YO',
            'RA','RI','RU','RE','RO',
            'WA','WI','WU','WE','WO',
            'SI','TI','TU',
            'XA','XI','XU','XE','XO',
            'LA','LI','LU','LE','LO',
            'VA','VI','VU','VE','VO',
            'FA','FI','FU','FE','FO',
            'QA','QI','QU','QE','QO',
            'A','I','U','E','O','N','-',',','.',':'
        );

    $k = array(
            'っB','っC','っD','っF','っG','っH','っJ','っK','っL','っM','ん','っP','っQ','っR','っS','っT','っV','っW','っX','っY','っZ',
            'か','き','く','け','こ',
            'が','ぎ','ぐ','げ','ご',
            'きゃ','きぃ','きゅ','きぇ','きょ',
            'ぎゃ','ぎぃ','ぎゅ','ぎぇ','ぎょ',
            'しゃ','し','しゅ','しぇ','しょ',
            'つ','さ','し','す','せ','そ',
            'ざ','じ','ず','ぜ','ぞ',
            'しゃ','しぃ','しゅ','しぇ','しょ',
            'じゃ','じ','じゅ','じぇ','じょ',
            'じゃ','じぃ','じゅ','じぇ','じょ',
            'っ','っ','た','ち','つ','て','と',
            'ぢゃ','ぢぃ','ぢゅ','ぢぇ','ぢょ',
            'でゃ','でぃ','でゅ','でぇ','でぃ',
            'だ','ぢ','づ','で','ど',
            'ちゃ','ち','ちゅ','ちぇ','ちょ',
            'ちゃ','ちぃ','ちゅ','ちぇ','ちょ',
            'な','に','ぬ','ね','の',
            'にゃ','にぃ','にゅ','にぇ','にょ',
            'てゃ','てぃ','てゅ','てぇ','てょ',
            'は','ひ','ふ','へ','ほ',
            'ば','び','ぶ','べ','ぼ',
            'ひゃ','ひぃ','ひゅ','ひぇ','ひょ',
            'びゃ','びょ','びゅ','びぇ','びょ',
            'ぱ','ぴ','ぷ','ぺ','ぽ',
            'ぴゃ','ぴぃ','ぴゅ','ぴぇ','ぴょ',
            'ま','み','む','め','も',
            'みゃ','みぃ','みゅ','みぇ','みょ',
            'りゃ','りぃ','りゅ','りぇ','りょ',
            'や','い','ゆ','いぇ','よ',
            'ら','り','る','れ','ろ',
            'わ','うぃ','う','うぇ','を',
            'し','ち','つ',
            'ぁ','ぃ','ぅ','ぇ','ぉ',
            'ぁ','ぃ','ぅ','ぇ','ぉ',
            'ヴぁ','ヴぃ','ヴ','ヴぇ','ヴぉ',
            'ふぁ','ふぃ','ふ','ふぇ','ふぉ',
            'くぁ','くぃ','く','くぇ','くぉ',
            'あ','い','う','え','お','ん','ー','、','。','.'
        );

      preg_match_all('/"(.*?)"/', $m, $match);  //正規表現で「”」で囲まれた部分を抽出
      $array = [];
      foreach ($match[1] as $key => $value) {
          $array[] = '"'.str_ireplace($r, $k, $value).'"';
      }
      $hiraganized = str_ireplace($array, $match[1], str_ireplace($r, $k, $m));
      return str_replace('"', '', $hiraganized);
  }

}
