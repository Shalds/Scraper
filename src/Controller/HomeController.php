<?php

namespace App\Controller;

use App\Entity\ModelProduct;
use App\Entity\Product;
use App\Entity\Site;
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
        $res = "";

        $modelProduct = new ModelProduct();

        $formModelProduct = $this->createForm(ModelProductType::class, $modelProduct);

        $mpRepo = $this->getDoctrine()->getRepository(ModelProduct::class);
        $siteRepo = $this->getDoctrine()->getRepository(Site::class);


        if ($request->get('model_product') != null) {
            $productNameTab = $request->get('model_product');

            $productNameR = $mpRepo->find($productNameTab['name']);
            $productName = $productNameR->getName();

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

            $urlSarenza = "https://www.sarenza.com/adidas-originals-" . $productName . "-mp";

            $urlZalando = "https://www.zalando.fr/" . $productName . "/";

            $tabUrl = ["sarenza" => $urlSarenza, "zalando" => $urlZalando];
            $em = $this->getDoctrine()->getManager();

            $siteRepo = $this->getDoctrine()->getRepository(Site::class);

            foreach ($tabUrl as $key => $item) {

                $response = $client->get($item);
                $html = $response->getBody();
                $crawler = new Crawler((string)$html);

                if ($key == "sarenza") {

                    $titleElement = $crawler->filter('.mighty.brand')->first();
                    $title = $titleElement->text();
                    $modelElement = $crawler->filter('.model')->first();
                    $model = $modelElement->text();
                    $prixElement = $crawler->filter('.infos .mighty.price')->first();
                    $prixSpace = trim($prixElement->text());

                    //parenthèse pour capturer le pattern
                    preg_match('([0-9,.]+)', $prixSpace, $prix);

                    $urlElement = $crawler->filter('.product-link')->first();
                    $url = $urlElement->attr("href");

                    //Get le siteId et modelId pour le tri par par ces deux entité.
                    $repoProduct = $this->getDoctrine()->getRepository(Product::class);
                    $siteR = $siteRepo->findOneBy(array("name" => $key));
                    $productR = $repoProduct->findOneBy(array("model" => $productNameR, "Site" => $siteR));

                    $productName = "";
                    $productUrl = "";
                    $productPrix = "";

                    //Gestion Image
                    $imgElement = $crawler->filter('.vignette .thumb img')->first();
                    $imgsrc = $imgElement->attr('src');

                    $nameFile = uniqid();
                    dump($nameFile);

                    file_put_contents('C:\xampp\htdocs\scraper\public\image\sarenza\stan-smith\\'.$nameFile.'', file_get_contents($imgsrc, false, $cxContext));


                    if ($productR != null) {
                        $productName = $productR->getName();
                        $productUrl = $productR->getUrl();
                        $productPrix = $productR->getPriceFinal();
                    }

                    //pas d'inscription dans la base de donné si les données correspondent
                    if ($productR == null OR $productName != $model && $productUrl != $url && $productPrix != (float)$prix[0]) {
                        $Product = new Product();
                        $Product->setName($model);
                        $Product->setPriceFinal((float)$prix[0]);
                        $Product->setUrl($url);
                        $Product->setModel($mpRepo->find($productNameTab['name']));
                        $Product->setSite($siteR);
                        $Product->setDateChange(new \DateTime());

                        $em->persist($Product);
                    }
                } elseif ($key == "zalando") {

                    $titleElement = $crawler->filter('.catalogArticlesList_brandName')->first();
                    $title = $titleElement->text();

                    $modelElement = $crawler->filter('.catalogArticlesList_articleName')->first();
                    $model = $modelElement->text();

                    $prixElement = $crawler->filter('.catalogArticlesList_price.specialPrice')->first();
                    $prixSpace = trim($prixElement->text());
                    preg_match('([0-9,.]+)', $prixSpace, $prix);

                    $urlElement = $crawler->filter('.catalogArticlesList_infoContent a')->first();
                    $url = $urlElement->attr("href");

                    //Get le siteId et modelId pour le tri par par ces deux entité.
                    $repoProduct = $this->getDoctrine()->getRepository(Product::class);
                    $siteR = $siteRepo->findOneBy(array("name" => $key));
                    $productR = $repoProduct->findOneBy(array("model" => $productNameR, "Site" => $siteR));

                    $productName = "";
                    $productUrl = "";
                    $productPrix = "";


                    if($productR != null){
                        $productName = $productR->getName();
                        $productUrl = $productR->getUrl();
                        $productPrix = $productR->getPriceFinal();
                    }

                    if ($productR == null OR $productName != $model && $productUrl != $url && $productPrix != (float)$prix[0]) {

                        //pas d'inscription dans la base de donné si les données correspondent
                        $Product = new Product();
                        $Product->setName($model);
                        $Product->setPriceFinal((float)$prix[0]);
                        $Product->setUrl($url);
                        $Product->setModel($mpRepo->find($productNameTab['name']));
                        $Product->setSite($siteR);
                        $Product->setDateChange(new \DateTime());

                        $em->persist($Product);

                    }
                }
                $em->flush();
            }
            $res = $repoProduct->findBy(array('model' => $productNameTab['name']));
        }

        return $this->render('home/home.html.twig', [
            "formModelProduct" => $formModelProduct->createView(),
            "res" => $res
        ]);
    }
}
