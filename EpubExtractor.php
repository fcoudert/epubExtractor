<?php
class EpubExtractor {

public static $fileName='';
public static $info= array();

function setFileName($f) {
    self::$fileName=$f;
}

function extractInfo() {
    if (self::$fileName!=='') {
        $zip = zip_open(self::$fileName);
        if (is_resource($zip)) {
            $chaine='';
            while ($file = zip_read($zip)) {
                $tmpName=zip_entry_name($file);
                if (strpos($tmpName,'.opf')!==false) {
                    $chaine=$tmpName;
                    zip_entry_open($zip, $file, "r");
                    $entry_content = zip_entry_read($file, zip_entry_filesize($file));
                    $xml = simplexml_load_string($entry_content);
                    if ($xml!==false) {
                    self::$info['title']=$xml->metadata->children('dc', true)->title;
                    self::$info['language']=$xml->metadata->children('dc', true)->language;
                    self::$info['creator']=$xml->metadata->children('dc', true)->creator;
                    self::$info['identifier']=$xml->metadata->children('dc', true)->identifier;
                    self::$info['publisher']=$xml->metadata->children('dc', true)->publisher;
                    self::$info['date']=$xml->metadata->children('dc', true)->date;
                    self::$info['description']=$xml->metadata->children('dc', true)->description;
                    //find meta with 'cover' name
                    $meta='';
                    self::$info['coverContent']='';
                    self::$info['coverImage']='';
                    for ($h=0;$h<count($xml->metadata->meta);$h++) {
                        foreach($xml->metadata->meta[$h]->attributes() as $a => $b) {
                            if ($a=='name' && $b=='cover') {
                                $meta=(array)$xml->metadata->meta[$h];
                                self::$info['coverContent']=$meta['@attributes']['content'];
                            }
                        }  
                    }
                    if (self::$info['coverContent']!=='') {
                        //Searching for item in manifest with id=$info['coverContent']  et save href value
                        for ($h=0;$h<count($xml->manifest->item);$h++) {
                            foreach($xml->manifest->item[$h]->attributes() as $a => $b) {
                                if ($a=='id' && $b==self::$info['coverContent']) {
                                    $meta=(array)$xml->manifest->item[$h];
                                    self::$info['coverImage']=$meta['@attributes']['href'];
                                }
                            }       
                        }
                    }
                    self::$info['coverData']=self::extractCover();
                   return (self::$info);
                }
                else {
                    return ('xml wrong format...');
                }   
                }
            }
            return ($chaine);
        }
        else {
            return ('invalid epub format');
        }
    }
    else {
        return ('no epub filename...');
    }

}


private  function extractCover() {
    
if (self::$fileName!=='') {
    $zip = zip_open(self::$fileName);
    if (is_resource($zip)) {
        $chaine='';
        if (self::$info['coverImage']==='') self::$info['coverImage']='cover.jpeg';
        //take only the fileName of cover name
        $tmp=explode("/",self::$info['coverImage']);
        $coverToFind=$tmp[count($tmp)-1];
        while ($file = zip_read($zip)) {
            $tmpName=zip_entry_name($file);
            if (strpos($tmpName,'cover.jpeg')!==false OR strpos($tmpName,'cover.jpg')!==false OR strpos($tmpName,$coverToFind)!==false) {
                $chaine=$tmpName;
                zip_entry_open($zip, $file, "r");
                $entry_content = zip_entry_read($file, zip_entry_filesize($file));
                return ($entry_content);
            }
        }
        return ('no image find');
    }
    else {
        return ('invalid epub format');
    }

}
else {
    return ('no epub filename is define...');
}

}





}
