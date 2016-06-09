<?php
namespace AsseticI18n\Model;

use AsseticI18n\Error\MissingTextEntryException;

class PrimaryStringProvider
{

    use\Zend\ServiceManager\ServiceLocatorAwareTrait;

    public function getPrimaryStringByCode($stringCode)
    {
        if (isset($this->getConfig()[$stringCode]))
            return $this->getConfig()[$stringCode];
        throw new MissingTextEntryException($stringCode);
    }

    /**
     *
     * @return array
     */
    private function getConfig()
    {
        $config = $this->getServiceLocator()->get('Config');
        if (! array_key_exists('text', $config))
            throw new \Exception("No text entry provided in Zend Configuration files");
        return $config['text'];
    }
}
