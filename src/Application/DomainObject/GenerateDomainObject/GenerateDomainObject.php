<?php

namespace Omatech\Hexagon\Application\DomainObject\GenerateDomainObject;

use Omatech\Hexagon\Application\Base\Instantiatable;
use Omatech\Hexagon\Domain\File\Interfaces\InstantiateRepository;

final class GenerateDomainObject
{
    use Instantiatable;

    /** @var InstantiateRepository */
    private $instantiateRepository;

    public function __construct(InstantiateRepository $instantiateRepository)
    {
        $this->instantiateRepository = $instantiateRepository;
    }

    public function execute(GenerateDomainObjectInputAdapter $request): GenerateDomainObjectOutputAdapter
    {
        $domain = $this->studlyNames($request->getDomain());

        $domainPath = config('hexagon.directories.domain.object', 'app/Domain/');
        $path = base_path($domainPath) . $domain;

        $file =  $domain . 'DO.php';

        if (file_exists($path . '/' . $file)) {
            return GenerateDomainObjectOutputAdapter::ofError('File Already Exists!', 'file_already_exists');
        }

        $template = $this->getTemplate('domain-object');

        $template = $this->replace('Domain', $domain, $template);
//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template, $path, $file);
        } catch (\Exception $e) {
            return GenerateDomainObjectOutputAdapter::ofError($e->getMessage());
        }

        return GenerateDomainObjectOutputAdapter::ofSuccess();
    }
}
