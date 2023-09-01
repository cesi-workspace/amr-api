<?php

namespace App\Tests\UnitTest;
use App\Entity\UserStatus;
use App\Service\MessageService;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;
use App\Service\Contract\IResponseValidatorService;
use App\Service\Contract\IUserService;

class MessageServiceTest extends TestCase
{
    public function testGetInfos()
    {
        // Mock the EntityManager and other dependencies
        $entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)
            ->getMock();

        $responseValidatorServiceMock = $this->getMockBuilder(IResponseValidatorService::class)
            ->getMock();

        $userServiceMock = $this->getMockBuilder(IUserService::class)
            ->getMock();

        $userFrom = new User();
        $userFrom->setCity("yt");
        $userFrom->setEmail("test.test@gmail.com");
        $userFrom->setFirstname("Test");
        $userFrom->setPostalCode("78000");
        $userFrom->setSurname("Test");

        $userTo = new User();
        $userTo->setCity("yt");
        $userTo->setEmail("titi.toto@gmail.com");
        $userTo->setFirstname("Toto");
        $userTo->setSurname("Titi");
        $userTo->setPostalCode("78000");

        // Create a sample Message object
        $message = new Message();
        $message->setId(1);
        $message->setContent('Test content');
        $message->setFromUser($userFrom);
        $message->setToUser($userTo);
        $message->setDate(new \DateTime('2023-08-31 12:34:56'));

        // Create an instance of MessageService and inject the mocks
        $messageService = new MessageService(
            $entityManagerMock,
            $responseValidatorServiceMock,
            $userServiceMock
        );

        // Call the method you want to test
        $result = $messageService->getInfos([$message]);

        // Perform assertions on the result
        $expectedResult = [
            [
                'id' => 1,
                'sender_name' => 'Test Test',
                'receiver_name' => 'Toto Titi',
                'content' => 'Test content',
                'date' => '2023-08-31 12:34:56'
            ]
        ];

        $this->assertSame($expectedResult, $result);
    }
}