use App\DataFixtures\AppFixtures;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AppFixturesTest extends KernelTestCase
{
    public function testPersistEvent()
    {
        self::bootKernel();
        $container = self::$container;

        $entityManager = $container->get('doctrine')->getManager();

        $fixture = new AppFixtures();
        $fixture->load($entityManager);

        // Assert that the event was persisted successfully
        $this->assertNotNull($entityManager->getRepository(Event::class)->findOneBy([]));
    }
}