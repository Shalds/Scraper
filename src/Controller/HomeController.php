<?php

namespace App\Controller;

use App\Entity\ModelProduct;
use App\Entity\Product;
use App\Form\ModelProductType;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\SetCookie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(Request $request)
    {
        $res ="";

        $modelProduct = new ModelProduct();

        $formModelProduct = $this->createForm(ModelProductType::class, $modelProduct);

        $mprepo = $this->getDoctrine()->getRepository(ModelProduct::class);


        if($request->get('model_product') != null){
            $productNameTab = $request->get('model_product');

            $productName = $mprepo->find($productNameTab['name'])->getName();

        //web socket ratchet
        //https://github.com/ratchetphp/Ratchet
            $client = new Client(array(
                'timeout' => 50,
                'verify' => false,
                'proxy' => 'http://10.100.0.248:8080',
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36',

                ]
            ));

            $aContext = array(
                'http' => array(
                    'proxy' => 'tcp://10.100.0.248:8080',
                    'request_fulluri' => true,
                ),
            );

            $cxContext = stream_context_create($aContext);

            $urlSarenza = "https://www.sarenza.com/adidas-originals-".$productName."-mp";

            $urlZalando = "https://www.zalando.fr/".$productName."/";

            $tabUrl = ["sarenza" => $urlSarenza, "zalando" => $urlZalando];
            $em = $this->getDoctrine()->getManager();

            foreach ($tabUrl as $key => $item){

                $response = $client->get($item);
                $html = $response->getBody();
                $crawler = new Crawler((string) $html);

                if($key == "sarenza"){

                    $titleElement = $crawler->filter('.mighty.brand')->first();
                    $title = $titleElement->text();
                    $modelElement = $crawler->filter('.model')->first();
                    $model = $modelElement->text();
                    $prixElement = $crawler->filter('.mighty.price')->first();
                    $prix = $prixElement->text();

                    $urlElement = $crawler->filter('.product-link')->first();
                    $url = $urlElement->text();



                    $Product = new Product();
                    $Product->setName($model);
                    $Product->setPriceFinal((float)$prix);
                    $Product->setUrl($url);


                    $em->persist($Product);
                    $em->flush();

                } elseif ($key == "zalando"){

                    $titleElement = $crawler->filter('.catalogArticlesList_brandName')->first();
                    $title = $titleElement->text();

                    $modelElement = $crawler->filter('.catalogArticlesList_articleName')->first();
                    $model = $modelElement->text();

                    $prixElement = $crawler->filter('.catalogArticlesList_price ')->first();
                    $prix = $prixElement->text();

                    $urlElement = $crawler->filter('.catalogArticlesList_infoContent a')->first();
                    $url = $urlElement->text();

                    $Product = new Product();
                    $Product->setName($model);
                    $Product->setPriceFinal((float)$prix);
                    $Product->setUrl($url);
                    $em->persist($Product);
                    $em->flush();
                }
            }

            $repoProduct = $this->getDoctrine()->getRepository(Product::class);
            $res = $repoProduct->findByString($productName);
        }
        
        return $this->render('home/home.html.twig', [

            "formModelProduct" => $formModelProduct->createView(),
            "res" => $res
        ]);
    }
}
