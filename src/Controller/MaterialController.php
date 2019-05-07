<?php

namespace App\Controller;

use App\Entity\Material;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class MaterialController
 * @package App\Controller
 *
 * @Route("/material")
 */
class MaterialController extends Controller
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="material")
     */
    public function index()
    {
        $materialRepo = $this->entityManager->getRepository(Material::class);
        $materials = $materialRepo->findWithQuantity();

        return $this->render('material/index.html.twig', [
            'materials' => $materials,
        ]);
    }

    /**
     * @param Material $material
     *
     * @Route("/{id}", name="material_show")
     */
    public function show(Material $material)
    {
        return $this->render('material/show.html.twig', [
            'material' => $material
        ]);
    }

    /**
     * @param Material $material
     *
     * @Route("/{id}/quantity", name="material_modify_quantity")
     */
    public function modifyQuantity(Request $request, Material $material)
    {
        switch ($request->query->get("type")) {
            case 'up_quantity':
                $material->setQuantity($material->getQuantity() + 1);
                break;
            case 'down_quantity':
                $material->setQuantity($material->getQuantity() - 1);
        }

        return new JsonResponse();
    }

}
