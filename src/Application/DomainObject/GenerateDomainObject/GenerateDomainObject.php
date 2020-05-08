<?php

namespace Omatech\Hexagon\Application\DomainObject\GenerateDomainObject;

use Omatech\Hexagon\Domain\String\StringToStudlyCaseRepository;
use Omatech\Hexagon\Domain\Template\GetRepository;
use Omatech\Hexagon\Domain\Template\InstantiateRepository;

final class GenerateDomainObject
{
    /** @var InstantiateRepository */
    private $instantiateRepository;
    /** @var GetRepository */
    private $getRepository;
    /** @var StringToStudlyCaseRepository */
    private $stringToStudlyCaseRepository;

    public function __construct(
        InstantiateRepository $instantiateRepository,
        GetRepository $getRepository,
        StringToStudlyCaseRepository $stringToStudlyCaseRepository
    )
    {
        $this->instantiateRepository = $instantiateRepository;
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

        $template->replace('Domain', $domain);
//        $template = $this->clearTemplate($template);

        try {
            $this->instantiateRepository->execute($template->getContent(), $path, $file);
        } catch (\Exception $e) {
            return GenerateDomainObjectOutputAdapter::ofError($e->getMessage());
        }

        return GenerateDomainObjectOutputAdapter::ofSuccess();
    }
}
