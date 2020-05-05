<?php

namespace Omatech\Hexagon\Application\DomainObject\GenerateDomainObject;

use Omatech\Hexagon\Domain\String\StringToStudlyCaseRepository;
use Omatech\Hexagon\Domain\Template\Interfaces\GetRepository;
use Omatech\Hexagon\Domain\Template\Interfaces\InstantiateRepository;
use Omatech\Hexagon\Domain\Template\Interfaces\ReplaceRepository;

final class GenerateDomainObject
{
    /** @var InstantiateRepository */
    private $instantiateRepository;
    /** @var ReplaceRepository */
    private $replaceRepository;
    /** @var GetRepository */
    private $getRepository;
    /** @var StringToStudlyCaseRepository */
    private $stringToStudlyCaseRepository;

    public function __construct(
        InstantiateRepository $instantiateRepository,
        ReplaceRepository $replaceRepository,
        GetRepository $getRepository,
        StringToStudlyCaseRepository $stringToStudlyCaseRepository
    )
    {
        $this->instantiateRepository = $instantiateRepository;
        $this->replaceRepository = $replaceRepository;
        $this->getRepository = $getRepository;
        $this->stringToStudlyCaseRepository = $stringToStudlyCaseRepository;
    }

    public function execute(GenerateDomainObjectInputAdapter $request): GenerateDomainObjectOutputAdapter
    {
        $domain = $this->stringToStudlyCaseRepository->execute($request->getDomain());

        $domainPath = config('hexagon.directories.domain.object', 'app/Domain/');
        $path = base_path($domainPath) . $domain;

        $file =  $domain . 'DO.php';

        if (file_exists($path . '/' . $file)) {
            return GenerateDomainObjectOutputAdapter::ofError('File Already Exists!', 'file_already_exists');
        }

        $template = $this->getRepository->execute('domain-object');

        $template = $this->replaceRepository->execute('Domain', $domain, $template);
//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template, $path, $file);
        } catch (\Exception $e) {
            return GenerateDomainObjectOutputAdapter::ofError($e->getMessage());
        }

        return GenerateDomainObjectOutputAdapter::ofSuccess();
    }
}
