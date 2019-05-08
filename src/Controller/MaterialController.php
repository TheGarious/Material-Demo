<?php

namespace App\Controller;

use App\Entity\Material;
use App\Form\MaterialType;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


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
            'materials' => $materials
        ]);
    }

    /**
     * @param Material $material
     *
     * @Route("/{id}", name="material_show")
     */
    public function show($id)
    {
        $repo = $this->getDoctrine()->getRepository(Material::class);

        $material = $repo->find($id);

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
    
    /**
     * 
     *@return Response
     * @Route("/{id}/edit", name="material_edit")
     */
    public function editMaterial(Material $material, Request $request, ObjectManager $manager)
    {


        $form = $this->createForm(MaterialType::class, $material);
        
        $form ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){


            $material = $form->getData();
            $manager->persist($material);
            $manager->flush();

            return $this->redirectToRoute('material');

        }


        return $this->render('material/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
