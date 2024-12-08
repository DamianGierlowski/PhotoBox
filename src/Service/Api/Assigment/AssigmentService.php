<?php
declare(strict_types=1);

namespace App\Service\Api\Assigment;

use App\Entity\Assigment;
use App\Repository\AssigmentRepository;
use App\Util\GuidFactory;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
class AssigmentService
{
    public function __construct(
       private AssigmentRepository $assigmentRepository,
       private EntityManagerInterface $entityManager,
    ) {
    }

    public function handleIndexRequest(UserInterface $user): array
    {
        $data = $this->assigmentRepository->findAllForUser($user);

        return $this->parseAssigments($data);
    }

    public function handleNewRequest(Request $request, UserInterface $user): void
    {
         $data = (array) json_decode($request->getContent());

         $assigment = new Assigment();
         $assigment->setUser($user)
             ->setTitle($data['title'])
             ->setDate(new \DateTime($data['date']))
             ->setGuid(GuidFactory::generate())
             ;

        $this->entityManager->persist($assigment);
        $this->entityManager->flush();
    }

    public function handleEditRequest(Request $request, UserInterface $user): void
    {
        $data = (array) json_decode($request->getContent());

        $assigment = $this->assigmentRepository->findOneByGuidForUser($data['guid'], $user);

        $assigment->setTitle($data['title'])
            ->setDate(new DateTime($data['date']));

        $this->entityManager->persist($assigment);
        $this->entityManager->flush();
    }

    public function handleDeleteRequest(string $guid, UserInterface $user): void
    {
        $assigment = $this->assigmentRepository->findOneByGuidForUser($guid, $user);

        if (null === $assigment) {
            // TODO handling
            return;
        }

        // TODO remove assigned gallery, and files

        $this->entityManager->remove($assigment);
        $this->entityManager->flush();
    }
    private function parseAssigments(array $assigments): array
    {
        $result = [];
        foreach ($assigments as $assigment) {
            $result[] = [
                'id' => $assigment->getId(),
                'title' => $assigment->getTitle(),
                'guid' => $assigment->getGuid(),
            ];
        }

        return $result;
    }

}