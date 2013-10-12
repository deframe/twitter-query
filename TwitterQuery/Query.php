<?php
namespace Dagrfr\TwitterQuery;

class Query
{
    protected $screenNames = array();
    protected $hashTags = array();
    protected $count;

    public function setScreenNames(array $screenNames)
    {
        $this->screenNames = $screenNames;
    }

    public function setScreenName($screenName)
    {
        $this->setScreenNames(array($screenName));
    }

    public function setHashTags(array $hashTags)
    {
        $this->hashTags = $hashTags;
    }

    public function setHashTag($hashTag)
    {
        $this->setHashTags(array($hashTag));
    }

    public function setCount($count)
    {
        $this->count = $count;
    }

    public function __toString()
    {
        $screenNamesString = '';
        $hashTagsString    = '';
        $queryString       = '';

        foreach ($this->screenNames as $screenName) {
            $screenNamesString .= (!empty($screenNamesString) ? ' OR ' : '') . 'from:' . $screenName;
        }

        foreach ($this->hashTags as $hashTag) {
            $hashTagsString .= (!empty($hashTagsString) ? ' OR ' : '') . $hashTag;
        }

        if (!empty($screenNamesString) || !empty($hashTagsString)) {
            $queryString .= 'q=';
        }

        if (!empty($screenNamesString)) {
            $queryString .= urlencode('(' . $screenNamesString . ')');
        }

        $queryString .= !empty($screenNamesString) && !empty($hashTagsString) ? '%20' : '';

        if (!empty($hashTagsString)) {
            $queryString .= urlencode('(' . $hashTagsString . ')');
        }

        if (isset($this->count)) {
            $queryString .= '&count=' . $this->count;
        }

        return $queryString;
    }
}