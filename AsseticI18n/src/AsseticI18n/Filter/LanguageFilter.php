<?php
namespace AsseticI18n\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class LanguageFilter implements FilterInterface
{
    /** @var string Locale identifier (us_US | fr_FR | de_DE | ...) */
    private $targetLocale;

    private static $stringCodePattern = "/(['\"])\{([^}\"']+)\}['\"]/";

    use \Zend\ServiceManager\ServiceLocatorAwareTrait;

    /**
     * Constructor
     * @param string $targetLocale en_EN | fr_FR | de_DE | ...
     */
    public function __construct($targetLocale)
    {
        $this->targetLocale = $targetLocale;
    }

    public function filterLoad(AssetInterface $asset)
    {}

    public function filterDump(AssetInterface $asset)
    {
        $content = $asset->getContent();
        $matches = array();
        $patternSearchResult = preg_match_all(self::$stringCodePattern, $content, $matches);
        if (false === $patternSearchResult) {
            // TODO log something
            return;
        }

        if (0 === $patternSearchResult) {
            // nothing to internationalize
            return;
        }

        $translator = $this->getTranslator();
        $globalMatches = $matches[0];
        $separators = $matches[1];
        $expressions = $matches[2];
        for ($i = 0; $i < count($globalMatches); $i ++) {
            $globalMatch = $globalMatches[$i];
            $separator = $separators[$i];
            $stringCode = $expressions[$i];
            $primaryString = $this->getPrimaryString($stringCode);
            $translatedString = $translator->translate($primaryString, 'default', $this->targetLocale);
            $content = str_replace($globalMatch, $separator . $translatedString . $separator, $content);
        }
        $asset->setContent($content);
    }

    private function getPrimaryString($stringCode)
    {
        return $this->getPrimaryStringProvider()->getPrimaryStringByCode($stringCode);
    }

    /**
     *
     * @return \AsseticI18n\Model\PrimaryStringProvider
     */
    private function getPrimaryStringProvider()
    {
        return $this->getServiceLocator()->get('primary-string-provider');
    }

    /**
     *
     * @return \Zend\Mvc\I18n\Translator
     */
    private function getTranslator()
    {
        return $this->getServiceLocator()->get('translator');
    }
}
