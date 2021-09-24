<?php

namespace Liquidaciones\CuposAnualesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\CuposAnualesBundle\Entity\Liquidaciones;
use Liquidaciones\CuposAnualesBundle\Form;
use Symfony\Component\HttpFoundation\Response;
use Liquidaciones\CuposAnualesBundle\Entity\PersonalCargoWeb;
use Liquidaciones\CuposAnualesBundle\Entity\Cuentas;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Source\Vector;
use APY\DataGridBundle\Grid\Column;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\SecurityContextInterface;
use \PHPExcel;
use \PDO;
/**
 * Liquidaciones controller.
 *
 * @Route("/liquidaciones")
 */
class LiquidacionesController extends Controller
{



    /**
     * Lists all Liquidaciones entities.
     *
     * @Route("/", name="liquidaciones")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:Liquidaciones')->findAll();

        return array(
            'entities' => $entities,
        );
    }


    /**
     * Lists all Liquidaciones entities.
     *
     * @Route("/cuposliquidacion/", name="liquidaciones_cuposliquidacion")
     * @Method("GET|POST")
     * @Template()
     */
    public function cuposliquidacionAction()
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");


        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        //Seguridad
        $securityContext = $this->get('security.context');


        /*if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }*/

        $cadRoles = '';
        $permisosCuentas = '';
        $lstRoles = $securityContext->getToken()->getUser()->getRoles();



        foreach ($lstRoles as $value) {
            //die(var_dump($value.'cristian'));
            switch ($value) {
                case ('ROLE_1'):
                    $cadRoles .= "1,";
                    //break;
                case ('ROLE_2'):
                    $cadRoles .= "2,";
                    //break;;
                case ('ROLE_3'):
                    $cadRoles .= "1,";
                    //break;
                case ('ROLE_4'):
                    $cadRoles .= "2,3,";
                    //break;
                case ('ROLE_5'):
                    $cadRoles .= "18,19,";
                    //break;
                case ('ROLE_6'):
                    $cadRoles .= "5,6,7,8,9,10,11,12,13,14,15,16,17,";
                    //break;
                case ('ROLE_7'):
                    $cadRoles .= "20,21,";
                    //break;
                case ('ROLE_8'):
                    $cadRoles .= "22,23,";
                    //break;
                case ('ROLE_9'):
                    $cadRoles .= "1,";
                    //break;
                case ('ROLE_10'):
                    $cadRoles .= "2,3,";
                    //break;
                case ('ROLE_11'):
                    $cadRoles .= "18,19,";
                    //break;
                case ('ROLE_12'):
                    $cadRoles .= "5,6,7,8,9,10,11,12,13,14,15,16,17,";
                    break;
                case ('ROLE_13'):
                    $cadRoles .= "20,21,";
                    //break;
                case ('ROLE_14'):
                    $cadRoles .= "22,23,";
                    //break;
				case ('ROLE_20'):
                    $cadRoles .= "24,25,";
                 case ('ROLE_22'):
                         $cadRoles .= "26,27,";
                    //break;
                default:
                    break;
            }

        }

        $where = "";
        if ($cadRoles == '') {

            $sql =       "select ca.id, c.anio,(RIGHT('00' + convert(varchar(2),c.mes),2)) as mes,c.monto,e.cupoEstado,(RIGHT('0000' + convert(varchar(4),d.iddependencia),4) + '-' + d.dependencia) as codigo,cc.cuenta,ca.adicional,a.descripcion,fec.fechaHasta,fec.fechaDesde
                         FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                        inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                        inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                        inner join Haberes.general.HADependencias d on c.iddependencia = d.iddependencia
                        inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                        inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta and cc.id in (6,7,8,9,10,11,15,16,17,21,22,23,24,25,26)
                        left join LiquidacionesWeb.dbo.FechaCierre fec
                        on          fec.refCupo = c.id
                        where convert(date, convert(varchar,c.mes) + '/01' +  '/' + convert(varchar,c.anio)) > DATEADD(MM, -5, getdate())";

            $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

            $entities = $connection ->  prepare($sql);

            $entities -> execute();
        } else {
            //die(var_dump($cadRoles));
            $permisosCuentas = ' and cc.id in ('.substr($cadRoles,0,strlen($cadRoles)-1).')';
            if ((true === $securityContext->isGranted('ROLE_1')) || (true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_15')) || (true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17')) || (true === $securityContext->isGranted('ROLE_18'))) {

                $sql =       "select ca.id, c.anio,(RIGHT('00' + convert(varchar(2),c.mes),2)) as mes,c.monto,e.cupoEstado,(RIGHT('0000' + convert(varchar(4),d.iddependencia),4) + '-' + d.dependencia) as codigo,cc.cuenta,ca.adicional,a.descripcion,fec.fechaHasta,fec.fechaDesde
                         FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                        inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                        inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                        inner join Haberes.general.HADependencias d on c.iddependencia = d.iddependencia
                        inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                        inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta and cc.id in (6,7,8,9,10,11,15,16,17,21,22,23,24,25,26,27)
                        left join LiquidacionesWeb.dbo.FechaCierre fec
                        on          fec.refCupo = c.id
                        WHERE d.iddependencia = '".$usuarioDependencia."' and convert(date, convert(varchar,c.mes) + '/01' +  '/' + convert(varchar,c.anio)) > DATEADD(MM, -5, getdate())";

                $connection = $this -> getDoctrine()
                                    -> getManager("ms_haberes_web")
                                    -> getConnection();

                $entities = $connection ->  prepare($sql);

                $entities -> execute();

            } else {

                $sql =       "select ca.id, c.anio,c.mes,c.monto,e.cupoEstado,(RIGHT('0000' + convert(varchar(4),d.iddependencia),4) + '-' + d.dependencia) as codigo,cc.cuenta,ca.adicional,a.descripcion,fec.fechaHasta,fec.fechaDesde
                         FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                        inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                        inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                        inner join Haberes.general.HADependencias d on c.iddependencia = d.iddependencia
                        inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                        inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta and cc.id in (6,7,8,9,10,11,15,16,17,21,22,23,24,25,26,27)
                        left join LiquidacionesWeb.dbo.FechaCierre fec
                        on          fec.refCupo = c.id
                        WHERE convert(date, convert(varchar,c.mes) + '/01' +  '/' + convert(varchar,c.anio)) > DATEADD(MM, -5, getdate()) and d.iddependencia = '".$usuarioDependencia."'".$permisosCuentas;

                $connection = $this -> getDoctrine()
                                    -> getManager("ms_haberes_web")
                                    -> getConnection();

                $entities = $connection ->  prepare($sql);

                $entities -> execute();


            }
        }


        if (($securityContext->isGranted('ROLE_2')) || ($securityContext->isGranted('ROLE_9')) ||
            ($securityContext->isGranted('ROLE_10')) || ($securityContext->isGranted('ROLE_11')) ||
            ($securityContext->isGranted('ROLE_12')) || ($securityContext->isGranted('ROLE_13')) ||
            ($securityContext->isGranted('ROLE_20')) || ($securityContext->isGranted('ROLE_14'))) {
            $puedeCargar = 1;
         } else {
             $puedeCargar = 0;
         }

         if ($securityContext->isGranted('ROLE_18')) {
             $puedeAutorizar = 1;
         } else {
             $puedeAutorizar = 0;
         }

        return array(
            'entities' => $entities,
            'pagina'=>0,
            'usuaCarga'=>0,
            'usuaAutoriza'=>0,
        );
    }




    /**
     * Lists all Liquidaciones entities.
     *
     * @Route("/historico/{persona}", name="liquidaciones_historico")
     * @Method("GET|POST")
     * @Template()
     */
    public function historicoAction($persona)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        //Seguridad
        $securityContext = $this->get('security.context');


        /*if (false === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }*/

        $cadRoles = '';
        $permisosCuentas = '';
        $lstRoles = $securityContext->getToken()->getUser()->getRoles();



        $spTraerPagos     = "exec haberes.Liquidacion.spTraerLiquidacionesPorPersonaTipoLiquidacion ".$persona.",'39|24|15|35|31|'";

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
        $stmt = $conn->prepare($spTraerPagos);
        $stmt->execute();

        $rResult = '';

        $rResult = $stmt->fetchAll();

        //$entities = $em->createQuery($sql)->getResult();
        $entities = $rResult;


        if (($securityContext->isGranted('ROLE_2')) || ($securityContext->isGranted('ROLE_9')) ||
            ($securityContext->isGranted('ROLE_10')) || ($securityContext->isGranted('ROLE_11')) ||
            ($securityContext->isGranted('ROLE_12')) || ($securityContext->isGranted('ROLE_13')) ||
            ($securityContext->isGranted('ROLE_20')) || ($securityContext->isGranted('ROLE_14'))) {
            $puedeCargar = 1;
         } else {
             $puedeCargar = 0;
         }

         if ($securityContext->isGranted('ROLE_18')) {
             $puedeAutorizar = 1;
         } else {
             $puedeAutorizar = 0;
         }


         $session = $this->getRequest()->getSession();

        if ($securityContext->isGranted('ROLE_20')) {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }

        $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$persona.',4,'.$usuarioDependencia;

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();

        $rResult = 0;

        $rResult = $stmt->fetchAll();

        return array(
            'entities' => $entities,
            'resultados'=>$rResult,
            'pagina'=>0,
            'usuaCarga'=>0,
            'usuaAutoriza'=>0,
        );
    }

    /**
     * Creates a new Liquidaciones entity.
     *
     * @Route("/", name="liquidaciones_create")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Liquidaciones:new.html.twig")
     */
    public function createAction(Request $request)
    {
        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_9')) ||
            (true === $securityContext->isGranted('ROLE_10')) || (true === $securityContext->isGranted('ROLE_11')) ||
            (true === $securityContext->isGranted('ROLE_12')) || (true === $securityContext->isGranted('ROLE_13')) ||
            (true === $securityContext->isGranted('ROLE_14')) || (true === $securityContext->isGranted('ROLE_16')) ||
            (true === $securityContext->isGranted('ROLE_20')) || (true === $securityContext->isGranted('ROLE_17')) || (true === $securityContext->isGranted('ROLE_22'))
                ){



        $HAtlCupo = $this->getRequest()->getSession()->get('hatlcupo');
        $idcupo = $this->getRequest()->getSession()->get('cupo');
        $idtipoliquidacion = $this->getRequest()->getSession()->get('idtipoliquidacion');

        $entity = new Liquidaciones();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $data = $request->request->all();
        //return var_dump($data);
        //$name = $data['form']['name'];

        $usuarioAutoriza = $data['usuarioautoriza'];

        if (isset($data['motivos'])) {
            $rGIdNovedad     = $data['motivos'];
        } else {
            $rGIdNovedad     = 0;
        }

        $rResultDiasGuardias = 0;
        $rResultDiasGuardiasRG = 0;

        $observacionConcepto = '';


        $idpersonalcargo = (integer)$this->getRequest()->getSession()->get('personal');
        $entity->setIdPersonalCargo($idpersonalcargo);
        $entity->setRGIdNovedad($rGIdNovedad);

        //$entity->setRGFecha($this->getRequest()->getSession()->get('fechanovedad')); // nuevo

        // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
        if ($this->cupoCerradoPorId($idcupo) == true) {
            $direccion = "liquidaciones_cuposliquidacion";
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
            );
            return $this->redirect($this->generateUrl($direccion));
        }
        ////////////////////////// FIN  ////////////////////////////////////////////////////
        //

        //$entity->setRGFecha(null);
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $cuposhatl = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($idcupo);


        $idpersonalcargoreemplazado = (integer)$this->getRequest()->getSession()->get('refpersonalReemplazo');

        //
        //$entity->setFechaCrea(new \DateTime());
        //$entity->setRGFecha(null);

        if ($idpersonalcargoreemplazado == 0) {
            /*
            if ((($idtipoliquidacion == 40) || ($idtipoliquidacion == 24)) && ($usuarioAutoriza == 0)) {
                $direccion = "liquidaciones_new";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'Es obligatorio cargar el agente que será reemplazado.'
                );
                return $this->redirect($this->generateUrl($direccion,array('persona'=>$idpersonalcargo)));

            } else {
                $entity->setRequiereAutorizacion(1);
            }
             *
             */
            $entity->setRequiereAutorizacion(0);
            $entity->setRGIdPersonalCargo(0);
        } else {
            $entity->setRGIdPersonalCargo($idpersonalcargoreemplazado);

        }

        $horasParaCargar = 0;
        if ($idtipoliquidacion == 40) {
            switch ($entity->getIdConcepto()) {
                case '515':
                    $entity->setIdConcepto(365);
                    $horasParaCargar = 12;
                    break;
                case '514':
                    $entity->setIdConcepto(364);
                    $horasParaCargar = 24;
                    break;
                case '517':
                    $entity->setIdConcepto(367);
                    $horasParaCargar = 12;
                    break;
                case '516':
                    $entity->setIdConcepto(366);
                    $horasParaCargar = 24;
                    break;
            }
        } else {
            switch ($entity->getIdConcepto()) {
                case '515':
                    $horasParaCargar = 12;
                    break;
                case '514':
                    $horasParaCargar = 24;
                    break;
                case '517':
                    $horasParaCargar = 12;
                    break;
                case '516':
                    $horasParaCargar = 24;
                    break;
            }
        }


        // controlo si sobrepasa las vacantes siempre y cuando no cargo un reemplazado
        if (($idpersonalcargoreemplazado == 0) && ( ($this->devolverVacantesTotalesHoras($cuposhatl->getCupos()->getId()) - $this->devolverTotalHorasRG($cuposhatl->getCupos()->getId()) - $horasParaCargar) < 0)) {


            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'Ha superado la cantidad de Vacantes Mensuales"'
            );
            return $this->redirect($this->generateUrl('liquidaciones_nomina',array('refCupo' => $idcupo)));
        }

        //die(var_dump($this->devolverVacantesTotalesHoras($cuposhatl->getCupos()->getId()) - $this->devolverTotalHorasRG($cuposhatl->getCupos()->getId()) - $valorHoras));

        ////////////////////////// FIN  ////////////////////////////////////////////////////

        $entity->setRefCupoTipoLiquidacion($HAtlCupo);
        $entity->setCuposhatipoliquidacion($cuposhatl);

        //die(var_dump($cuposhatl->getCupos()->getMonto()));

        /////////////////////// PARA LOS ACUMULADORES /////////////////
        $Fecha = $em->createQuery('SELECT c.anio, c.mes FROM LiquidacionesCuposAnualesBundle:Cupos c
                  WHERE c.id = :idCupo
                  GROUP BY c.anio, c.mes')->setParameter('idCupo', $cuposhatl->getCupos()->getId())->getResult();
        $anio = $Fecha[0]["anio"];
        $mes = $Fecha[0]["mes"];

        $total = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $cuposhatl->getCupos()->getId())->getResult();

        if ($total != null) {
            $totalGastadoCupoMensual = $total[0]["total"] == 0 ? 0 : $total[0]["total"];
        } else {
            $totalGastadoCupoMensual = 0;
        }
        $personalcargo = $this->getRequest()->getSession()->get('personal');

        $gastado24hs = 0;
        $gastado12hs = 0;
        $gastado24hsferiado = 0;
        $gastado12hsferiado = 0;


        if ($idtipoliquidacion != 40) {


            //TRAIGO EL VALOR DE LA GUARDIA /////////////
            $fechanovedadCorta = $mes.'/'.$anio;
                $em = $this->getDoctrine()->getManager("ms_haberes_web");
                $query = $em->createQuery(
                    "SELECT cc.monto
                       FROM LiquidacionesHaberesBundle:HAConceptosValor cc
                      WHERE cc.refConcepto in (514,515,516,517) and '".date("Y-d-m", strtotime("01/".$fechanovedadCorta))."' >= cc.vigDesde and '".date("Y-d-m", strtotime("01/".$fechanovedadCorta))."' <= cc.vigHasta
                      order by cc.refConcepto"
                );

                $conceptosValor = $query->getResult();

                $concepto24hs = $conceptosValor[0]['monto'];
                $concepto12hs = $conceptosValor[1]['monto'];
                $concepto24hsferiado = $conceptosValor[2]['monto'];
                $concepto12hsferiado = $conceptosValor[3]['monto'];
            /////////////////////////////////////////////

            if ($cuposhatl->getCuentas()->getId() == 24) {

                    $totalRGsql = "SELECT
                        SUM((case WHEN l.idConcepto = 515 THEN l.rGCantHsGuardia ELSE 0 END)) as docehs,
                        SUM((case WHEN l.idConcepto = 514 THEN l.rGCantHsGuardia ELSE 0 END)) as veinticuatrohs,
                        SUM((case WHEN l.idConcepto = 517 THEN l.rGCantHsGuardia ELSE 0 END)) as docehsferiado,
                        SUM((case WHEN l.idConcepto = 516 THEN l.rGCantHsGuardia ELSE 0 END)) as veinticutrohsferiado
                    FROM LiquidacionesWeb.dbo.Liquidaciones l
                  INNER JOIN LiquidacionesWeb.dbo.cuposhatiposliquidacion ca ON l.refcupotipoliquidacion = ca.id
                  INNER JOIN LiquidacionesWeb.dbo.cupos c ON c.id = ca.refcupo
                  WHERE ( year(l.rGFecha) = ".((integer)$entity->getRGFecha()->format('Y'))." and month(l.rGFecha) = ".((integer)$entity->getRGFecha()->format('m'))." AND l.idPersonalCargo = ".$personalcargo.") and c.RefCupoEstado in (1,2)";

                    $connection = $this -> getDoctrine()
                                        -> getManager("ms_haberes_web")
                                        -> getConnection();

                    $totalRG = $connection ->  prepare($totalRGsql);

                    $totalRG->execute();
                    //$totalRG->fetchAll();


                    if ($totalRG != null) {
                        foreach ($totalRG as $a) {
                            $totalRG12 = $a["docehs"] == null ? 0 : $a["docehs"];
                            $totalRG24 = $a["veinticuatrohs"] == null ? 0 : $a["veinticuatrohs"];
                            $totalRG12F = $a["docehsferiado"] == null ? 0 : $a["docehsferiado"];
                            $totalRG24F = $a["veinticutrohsferiado"] == null ? 0 : $a["veinticutrohsferiado"];
                        }


                    } else {
                        $totalRG12 = 0;
                        $totalRG24 = 0;
                        $totalRG12F = 0;
                        $totalRG24F = 0;
                    }


                $em = $this->getDoctrine()->getManager("ms_haberes_web");
                $sql = "EXEC haberes.Liquidacion.spTraerSaldoHorasReemplazosguardia ".$personalcargo.",".$entity->getRGFecha()->format('m').",".$entity->getRGFecha()->format('Y').",'".$entity->getRGFecha()->format('Y-m-d')."'";

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmt = $conn->prepare($sql);

                $stmt->execute();
                $rResultSaldosRG = 0;
                $rResultSaldosRG = $stmt->fetchAll();
                //die(var_dump($rResultSaldosRG[0]['Conc602']));
                //die(var_dump($rResultSaldosRG));

                $gastado24hs = $rResultSaldosRG[0]['Conc601'];
                $gastado12hs = $rResultSaldosRG[0]['Conc602'];
                $gastado24hsferiado = $rResultSaldosRG[0]['Conc603'];
                $gastado12hsferiado = $rResultSaldosRG[0]['Conc604'];


                $direccion = "liquidaciones_newdeuda";
            } else {

                $totalRG = $em->createQuery('SELECT
                        sum((case when l.idConcepto = 515 then l.rGCantHsGuardia else 0 end)) as docehs,
                        sum((case when l.idConcepto = 514 then l.rGCantHsGuardia else 0 end)) as veinticuatrohs,
                        sum((case when l.idConcepto = 517 then l.rGCantHsGuardia else 0 end)) as docehsferiado,
                        sum((case when l.idConcepto = 516 then l.rGCantHsGuardia else 0 end)) as veinticutrohsferiado
                    FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  JOIN ca.cupos c
                  WHERE (c.RefCupoEstado = 2 and ca.refCuenta <> 24 and c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('Anio' => $anio, 'Mes'=> $mes,'idPersonalC'=> $personalcargo,))->getResult();

                $direccion = "liquidaciones_new";

                if ($totalRG != null) {
                    $totalRG12 = $totalRG[0]["docehs"] == null ? 0 : $totalRG[0]["docehs"];
                    $totalRG24 = $totalRG[0]["veinticuatrohs"] == null ? 0 : $totalRG[0]["veinticuatrohs"];
                    $totalRG12F = $totalRG[0]["docehsferiado"] == null ? 0 : $totalRG[0]["docehsferiado"];
                    $totalRG24F = $totalRG[0]["veinticutrohsferiado"] == null ? 0 : $totalRG[0]["veinticutrohsferiado"];

                } else {
                    $totalRG12 = 0;
                    $totalRG24 = 0;
                    $totalRG12F = 0;
                    $totalRG24F = 0;
                }
            }
        } else {

            //TRAIGO EL VALOR DE LA GUARDIA /////////////

            $fechanovedadCorta = $mes.'/'.$anio;
                $em = $this->getDoctrine()->getManager("ms_haberes_web");
                $query = $em->createQuery(
                    "SELECT cc.monto
                       FROM LiquidacionesHaberesBundle:HAConceptosValor cc
                      WHERE cc.refConcepto in (364,365,366,367) and '".date("Y-d-m", strtotime("01/".$fechanovedadCorta))."' >= cc.vigDesde and '".date("Y-d-m", strtotime("01/".$fechanovedadCorta))."' <= cc.vigHasta
                      order by cc.refConcepto"
                );

                $conceptosValor = $query->getResult();

                $concepto24hs = $conceptosValor[0]['monto'];
                $concepto12hs = $conceptosValor[1]['monto'];
                $concepto24hsferiado = $conceptosValor[2]['monto'];
                $concepto12hsferiado = $conceptosValor[3]['monto'];
            /////////////////////////////////////////////


                $totalRG = $em->createQuery('SELECT
                        sum((case when l.idConcepto = 365 then l.rGCantHsGuardia else 0 end)) as docehs,
                        sum((case when l.idConcepto = 364 then l.rGCantHsGuardia else 0 end)) as veinticuatrohs,
                        sum((case when l.idConcepto = 367 then l.rGCantHsGuardia else 0 end)) as docehsferiado,
                        sum((case when l.idConcepto = 366 then l.rGCantHsGuardia else 0 end)) as veinticutrohsferiado
                    FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  JOIN ca.cupos c
                  WHERE (c.RefCupoEstado = 2 and ca.refCuenta <> 24 and c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('Anio' => $anio, 'Mes'=> $mes,'idPersonalC'=> $personalcargo,))->getResult();

                $direccion = "liquidaciones_new";




            /*** ESTA OPCION SE QUITO A PEDIDO DE PEDRO
            if ($idpersonalcargoreemplazado == 0) {
                //die(var_dump($persona));
                $direccion = "liquidaciones_new";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'Es obligatorio cargar el Reemplazado. '
                );
                return $this->redirect($this->generateUrl($direccion,array('persona'=>$idpersonalcargo, 'personarg'=> $idpersonalcargoreemplazado=='' ? 0 : $idpersonalcargoreemplazado, 'fechaguardia'=> $entity->getRGFecha()->format('d-m-Y'))));
            }
            */
            if ($totalRG != null) {
                $totalRG12 = $totalRG[0]["docehs"] == null ? 0 : $totalRG[0]["docehs"];
                $totalRG24 = $totalRG[0]["veinticuatrohs"] == null ? 0 : $totalRG[0]["veinticuatrohs"];
                $totalRG12F = $totalRG[0]["docehsferiado"] == null ? 0 : $totalRG[0]["docehsferiado"];
                $totalRG24F = $totalRG[0]["veinticutrohsferiado"] == null ? 0 : $totalRG[0]["veinticutrohsferiado"];

            } else {
                $totalRG12 = 0;
                $totalRG24 = 0;
                $totalRG12F = 0;
                $totalRG24F = 0;
            }

        }





        //////////////////////  FIN ///////////////////////////////////

        //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
        $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$personalcargo.',4';

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $rResult = 0;

        $rResult = $stmt->fetchAll();

        if ($this->getRequest()->getSession()->get('refpersonalReemplazo') != null) {
            $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$this->getRequest()->getSession()->get('refpersonalReemplazo').',4';

            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $rResultReemplazo = 0;

            $rResultReemplazo = $stmt->fetchAll();
            //die(var_dump($entity->getRGFecha()->format('N')));
            //Verifico que el reemplazado tenga saldo de horas para ser reemplazado/////////////////
            $sqlVerificaSaldoHoras = 'EXEC haberes.haberes.spValidaReemplazoMismoDiaSemana '.$idpersonalcargoreemplazado.','.$idpersonalcargo.','.$entity->getRGFecha()->format('N').",'".$entity->getRGFecha()->format('Y-m-d')."'";

            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
            $stmt = $conn->prepare($sqlVerificaSaldoHoras);
            $stmt->execute();
            $rResultVerificaSaldoHoras = 0;

            $rResultVerificaSaldoHoras = $stmt->fetchAll();
        } else {
            $rResultReemplazo = 0;
        }


            //die(var_dump($entity->getMontoTotalCalculado()));
        if (($entity->getMontoTotalCalculado() == 0) || ($entity->getMontoTotalCalculado()== null)) {
            //die(var_dump($persona));
            $direccion = "liquidaciones_new";
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'El monto total debe ser mayor a cero. '
            );
            return $this->redirect($this->generateUrl($direccion,array('persona'=>$idpersonalcargo, 'personarg'=> $idpersonalcargoreemplazado=='' ? 0 : $idpersonalcargoreemplazado, 'fechaguardia'=> $entity->getRGFecha()->format('d-m-Y'))));
        }

        //////////////////////////   VALIDACIONES /////////////////////////////////////////////////

        if ($cuposhatl->getCupos()->getMonto() - ($totalGastadoCupoMensual + $entity->getMontoTotalCalculado()) < 0) {
            $direccion = "liquidaciones_new";
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'El monto total supera el monto del Cupo Mensual.'
            );
            return $this->redirect($this->generateUrl($direccion,array('persona'=>$idpersonalcargo, 'personarg'=> $idpersonalcargoreemplazado=='' ? 0 : $idpersonalcargoreemplazado, 'fechaguardia'=> $entity->getRGFecha()->format('d-m-Y'))));
        }

        $totalAlexandraMartinoli = $gastado12hsferiado + $gastado12hs + $gastado24hsferiado + $gastado24hs + ($totalRG12 * 12) + ($totalRG12F * 12) + ($totalRG24 * 24) + ($totalRG24F * 24) + ($horasParaCargar * $entity->getRGCantHsGuardia());

        //die(var_dump($totalAlexandraMartinoli));
        //die(var_dump($totalAlexandraMartinoli));
        if ($totalAlexandraMartinoli > 120) {

            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'Supera las 120 Horas.'
            );
            return $this->redirect($this->generateUrl($direccion,array('persona'=>$idpersonalcargo, 'personarg'=> $idpersonalcargoreemplazado=='' ? 0 : $idpersonalcargoreemplazado, 'fechaguardia'=> $entity->getRGFecha()->format('d-m-Y'))));
        }
        ///////////////////////////////////////////////////////////////////////////////////////////

        //$em->getConnection()->beginTransaction();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager("ms_haberes_web");
            $em->persist($entity);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'aviso_ok',
                'Los datos fueron guardados correctamente!'
            );

            return $this->redirect($this->generateUrl('liquidaciones_show', array('id' => $entity->getId())));
        }

        //die(var_dump($cuposhatl->getCupos()->getMonto()));

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'resultados' => $rResult,
            'resultadosReemplazo' => $rResultReemplazo,
            'cupo' => $idcupo,
            'hacupo' => $HAtlCupo,
            'cupototal'=> $cuposhatl->getCupos()->getMonto(),
            'cupogastado'=>$totalGastadoCupoMensual,
            'rg12' => $totalRG12,
            'rg24' => $totalRG24,
            'rg12f'=> $totalRG12F,
            'rg24f'=> $totalRG24F,
            'conceptoValor24hs'=>$concepto24hs,
            'conceptoValor12hs'=>$concepto12hs,
            'conceptoValor24hsFeriado'=>$concepto24hsferiado,
            'conceptoValor12hsFeriado'=>$concepto12hsferiado,
            'diasGuardias'=>$rResultDiasGuardias,
            'diasGuardiasRG'=>$rResultDiasGuardiasRG,
            'mes' => $cuposhatl->getCupos()->getMes(),
            'anio'=> $cuposhatl->getCupos()->getAnio(),
            'fechanovedad'=>$entity->getRGFecha()->format('d-m-Y'),
            'observacionConcepto'=>$observacionConcepto,
        );

        } else {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado! '
            );

            return $this->redirect($this->generateUrl('inicio'));

        }
    }



    /**
     * Creates a new Liquidaciones entity.
     *
     * @Route("/", name="liquidaciones_deudacreate")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Liquidaciones:newdeuda.html.twig")
     */
    public function deudacreateAction(Request $request)
    {
        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_9')) ||
            (true === $securityContext->isGranted('ROLE_10')) || (true === $securityContext->isGranted('ROLE_11')) ||
            (true === $securityContext->isGranted('ROLE_12')) || (true === $securityContext->isGranted('ROLE_13')) ||
            (true === $securityContext->isGranted('ROLE_14')) || (true === $securityContext->isGranted('ROLE_16')) ||
            (true === $securityContext->isGranted('ROLE_20')) || (true === $securityContext->isGranted('ROLE_17'))
                ){



        $HAtlCupo = $this->getRequest()->getSession()->get('hatlcupo');
        $idcupo = $this->getRequest()->getSession()->get('cupo');
        $idtipoliquidacion = $this->getRequest()->getSession()->get('idtipoliquidacion');

        $entity = new Liquidaciones();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $data = $request->request->all();
        //return var_dump($data);
        //$name = $data['form']['name'];

        $usuarioAutoriza = $data['usuarioautoriza'];

        $rGIdNovedad     = $data['motivos'];

        $idpersonalcargo = (integer)$this->getRequest()->getSession()->get('personal');
        $entity->setIdPersonalCargo($idpersonalcargo);
        $entity->setRGIdNovedad($rGIdNovedad);

        // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
        if ($this->cupoCerradoPorId($idcupo) == true) {
            $direccion = "liquidaciones_cuposliquidacion";
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
            );
            return $this->redirect($this->generateUrl($direccion));
        }
        ////////////////////////// FIN  ////////////////////////////////////////////////////
        //
        //$entity->setFechaCrea(new \DateTime());
        //$entity->setRGFecha(null);
        $idpersonalcargoreemplazado = (integer)$this->getRequest()->getSession()->get('refpersonalReemplazo');
        if ($idpersonalcargoreemplazado == 0) {
            $entity->setRequiereAutorizacion(0);
            $entity->setRGIdPersonalCargo(0);
        } else {
            $entity->setRGIdPersonalCargo($idpersonalcargoreemplazado);

        }


        $horasParaCargar = 0;
        if ($idtipoliquidacion == 40) {
            switch ($entity->getIdConcepto()) {
                case '365':
                    $entity->setIdConcepto(365);
                    $horasParaCargar = 12;
                    break;
                case '364':
                    $entity->setIdConcepto(364);
                    $horasParaCargar = 24;
                    break;
                case '367':
                    $entity->setIdConcepto(367);
                    $horasParaCargar = 12;
                    break;
                case '366':
                    $entity->setIdConcepto(366);
                    $horasParaCargar = 24;
                    break;
            }
        } else {
            switch ($entity->getIdConcepto()) {
                case '515':
                    $entity->setIdConcepto(515);
                    $horasParaCargar = 12;
                    break;
                case '514':
                    $entity->setIdConcepto(514);
                    $horasParaCargar = 24;
                    break;
                case '517':
                    $entity->setIdConcepto(517);
                    $horasParaCargar = 12;
                    break;
                case '516':
                    $entity->setIdConcepto(516);
                    $horasParaCargar = 24;
                    break;
            }
        }

        $cuposhatl = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($idcupo);


        // controlo si sobrepasa las vacantes siempre y cuando no cargo un reemplazado
        if (($idpersonalcargoreemplazado == 0) && ( ($this->devolverVacantesTotalesHoras($cuposhatl->getCupos()->getId()) - $this->devolverTotalHorasRG($cuposhatl->getCupos()->getId()) - $horasParaCargar) < 0)) {


            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'Ha superado la cantidad de Vacantes Mensuales"'
            );
            return $this->redirect($this->generateUrl('liquidaciones_nomina',array('refCupo' => $idcupo)));
        }



        $em = $this->getDoctrine()->getManager("ms_haberes_web");



        $entity->setRefCupoTipoLiquidacion($HAtlCupo);
        $entity->setCuposhatipoliquidacion($cuposhatl);


        /////////////////////// PARA LOS ACUMULADORES /////////////////
        $Fecha = $em->createQuery('SELECT c.anio, c.mes FROM LiquidacionesCuposAnualesBundle:Cupos c
                  WHERE c.id = :idCupo
                  GROUP BY c.anio, c.mes')->setParameter('idCupo', $cuposhatl->getCupos()->getId())->getResult();
        $anio = $Fecha[0]["anio"];
        $mes = $Fecha[0]["mes"];

        $total = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $cuposhatl->getCupos()->getId())->getResult();

        if ($total != null) {
            $totalGastadoCupoMensual = $total[0]["total"] == 0 ? 0 : $total[0]["total"];
        } else {
            $totalGastadoCupoMensual = 0;
        }
        $personalcargo = $this->getRequest()->getSession()->get('personal');

        $totalRG = $em->createQuery('SELECT
                        sum((case when l.idConcepto in (515,365) then l.rGCantHsGuardia else 0 end)) as docehs,
                        sum((case when l.idConcepto in (514,364) then l.rGCantHsGuardia else 0 end)) as veinticuatrohs,
                        sum((case when l.idConcepto in (517,367) then l.rGCantHsGuardia else 0 end)) as docehsferiado,
                        sum((case when l.idConcepto in (516,366) then l.rGCantHsGuardia else 0 end)) as veinticutrohsferiado
                    FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  JOIN ca.cupos c
                  WHERE (c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('Anio' => $anio, 'Mes'=> $mes,'idPersonalC'=> $personalcargo,))->getResult();


        if ($totalRG != null) {
            $totalRG12 = $totalRG[0]["docehs"] == null ? 0 : $totalRG[0]["docehs"];
            $totalRG24 = $totalRG[0]["veinticuatrohs"] == null ? 0 : $totalRG[0]["veinticuatrohs"];
            $totalRG12F = $totalRG[0]["docehsferiado"] == null ? 0 : $totalRG[0]["docehsferiado"];
            $totalRG24F = $totalRG[0]["veinticutrohsferiado"] == null ? 0 : $totalRG[0]["veinticutrohsferiado"];

        } else {
            $totalRG12 = 0;
            $totalRG24 = 0;
            $totalRG12F = 0;
            $totalRG24F = 0;
        }

        //////////////////////  FIN ///////////////////////////////////





        //Vuelvo a buscar a la persona para trearme losd atos de esta/////////////////
        $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$personalcargo.',4';
        /*$stmt = $this   ->getDoctrine()
                        ->getManager('odbc_haberes')
                        ->getConnection()
                        ->prepare($sql);
        $rResult = $stmt->fetch();*/

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $rResult = 0;

        $rResult = $stmt->fetchAll();

        if ($this->getRequest()->getSession()->get('refpersonalReemplazo') != null) {
            $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$this->getRequest()->getSession()->get('refpersonalReemplazo').',4';
            /*$stmt = $this   ->getDoctrine()
                            ->getManager('odbc_haberes')
                            ->getConnection()
                            ->prepare($sql);
            $rResultReemplazo = $stmt->fetch();*/
            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $rResultReemplazo = 0;

            $rResultReemplazo = $stmt->fetchAll();
        } else {
            $rResultReemplazo = 0;
        }


            //die(var_dump($entity->getMontoTotalCalculado()));
        if (($entity->getMontoTotalCalculado() == 0) || ($entity->getMontoTotalCalculado()== null)) {
            //die(var_dump($persona));
            $direccion = "liquidaciones_newdeuda";
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'El monto total debe ser mayor a cero. '
            );
            return $this->redirect($this->generateUrl($direccion,array('persona'=>$personalcargo)));
        }

        //////////////////////////   VALIDACIONES /////////////////////////////////////////////////

        if ($cuposhatl->getCupos()->getMonto() - ($totalGastadoCupoMensual + $entity->getMontoTotalCalculado()) < 0) {
            $direccion = "liquidaciones_newdeuda";
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'El monto total supera el monto del Cupo Mensual.'
            );
            return $this->redirect($this->generateUrl($direccion,array('persona'=>$idpersonalcargo)));
        }
        ///////////////////////////////////////////////////////////////////////////////////////////

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager("ms_haberes_web");
            $em->persist($entity);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'aviso_ok',
                'Los datos fueron guardados correctamente!'
            );

            return $this->redirect($this->generateUrl('liquidaciones_show', array('id' => $entity->getId())));
        }

        //die(var_dump($cuposhatl->getCupos()->getMonto()));

        ///////////////////// si fallo ///////////////////////////////////////

        //TRAIGO LAS GUARDIAS QUE HIZO EN LA FECHA DE LA NOVEDAD /////////////
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $sql = "EXEC haberes.Liquidacion.spTraerSaldoHorasReemplazosguardia ".$persona.",".$entity->getRGFecha()->format('m').",".$entity->getRGFecha()->format('Y').",'".$entity->getRGFecha()->format('Y-m-d')."'";
        /*$stmt = $this   ->getDoctrine()
                        ->getManager('odbc_haberes')
                        ->getConnection()
                        ->prepare($sql);
        $rResult = $stmt->fetch();*/

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();
        $rResultSaldosRG = 0;
        $rResultSaldosRG = $stmt->fetchAll();

        $gastado24hs = $rResultSaldosRG[0]['Conc601'];
        $gastado12hs = $rResultSaldosRG[0]['Conc602'];
        $gastado24hsferiado = $rResultSaldosRG[0]['Conc603'];
        $gastado12hsferiado = $rResultSaldosRG[0]['Conc604'];
        $saldo = $rResultSaldosRG[0]['saldo'];

        /////////////////////////////////////////////


        //TRAIGO EL VALOR DE LA GUARDIA /////////////
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        if ($idtipoliquidacion == 40) {
            $query = $em->createQuery(
                "SELECT cc.monto
                   FROM LiquidacionesHaberesBundle:HAConceptosValor cc
                  WHERE cc.refConcepto in (364,365,366,367) and '".date('Y-m-d')."' >= cc.vigDesde and '".date('Y-m-d')."' <= cc.vigHasta
                  order by cc.refConcepto"
            );
        } else {
            $query = $em->createQuery(
                "SELECT cc.monto
                   FROM LiquidacionesHaberesBundle:HAConceptosValor cc
                  WHERE cc.refConcepto in (514,515,516,517) and '".date('Y-m-d')."' >= cc.vigDesde and '".date('Y-m-d')."' <= cc.vigHasta
                  order by cc.refConcepto"
            );
        }

        $conceptosValor = $query->getResult();

        $concepto24hs = $conceptosValor[0]['monto'];
        $concepto12hs = $conceptosValor[1]['monto'];
        $concepto24hsferiado = $conceptosValor[2]['monto'];
        $concepto12hsferiado = $conceptosValor[3]['monto'];
        /////////////////////////////////////////////

        //////          Traigo los dias de Guardi del Agente  /////////////////
        if (($this->getRequest()->getSession()->get('refpersonalReemplazo')!=0)) {

            $sqlDiasGuardiasRG = 'EXEC haberes.haberes.spDiasGuardiaWeb '.$this->getRequest()->getSession()->get('refpersonalReemplazo');
            /*$stmt = $this   ->getDoctrine()
                            ->getManager('odbc_haberes')
                            ->getConnection()
                            ->prepare($sql);
            $rResultReemplazo = $stmt->fetch();
            */
            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmtDiasGuardiasRG = $conn->prepare($sqlDiasGuardiasRG);

            $stmtDiasGuardiasRG->execute();
            $rResultDiasGuardiasRG = 0;
            $rResultDiasGuardiasRG = $stmtDiasGuardiasRG->fetchAll();
        }
        /////                  FIN                            /////////////////

        $novedades   =  $em->getRepository('LiquidacionesParteNovedadesBundle:Novedad')->findAll();

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'resultados' => $rResult,
            'resultadosReemplazo' => $rResultReemplazo,
            'cupo' => $idcupo,
            'hacupo' => $HAtlCupo,
            'cupototal'=> $cuposhatl->getCupos()->getMonto(),
            'cupogastado'=>$totalGastadoCupoMensual,
            'rg12' => $totalRG12 + ($gastado12hs/12),
            'rg24' => $totalRG24 + ($gastado24hs/24),
            'rg12f'=> $totalRG12F + ($gastado12hsferiado/12),
            'rg24f'=> $totalRG24F + ($gastado24hsferiado/24),
            'conceptoValor24hs'=>$concepto24hs,
            'conceptoValor12hs'=>$concepto12hs,
            'conceptoValor24hsFeriado'=>$concepto24hsferiado,
            'conceptoValor12hsFeriado'=>$concepto12hsferiado,
            'diasGuardias'=>$rResultDiasGuardias,
            'diasGuardiasRG'=>$rResultDiasGuardiasRG,
            'mes'=>$mes,
            'anio'=>$anio,
            'saldo' => $saldo,
            'novedad' => $novedades,
            'fechanovedad'=>$entity->getRGFecha()->format('d/m/Y'),
        );

        } else {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado! '
            );

            return $this->redirect($this->generateUrl('inicio'));

        }
    }



    /**
     * Creates a new Liquidaciones entity.
     *
     * @Route("/createhs/", name="liquidaciones_createhs")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Liquidaciones:newhs.html.twig")
     */
    public function createhsAction(Request $request)
    {
        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_9')) ||
            (true === $securityContext->isGranted('ROLE_10')) || (true === $securityContext->isGranted('ROLE_11')) ||
            (true === $securityContext->isGranted('ROLE_12')) || (true === $securityContext->isGranted('ROLE_13')) ||
            (true === $securityContext->isGranted('ROLE_14')) || (true === $securityContext->isGranted('ROLE_16')) ||
            (true === $securityContext->isGranted('ROLE_20')) || (true === $securityContext->isGranted('ROLE_17')) || (true === $securityContext->isGranted('ROLE_22'))
                ){


        $HAtlCupo = $this->getRequest()->getSession()->get('hatlcupo');

        $entity = new Liquidaciones();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $idpersonalcargo = (integer)$this->getRequest()->getSession()->get('refpersonal');
        $entity->setIdPersonalCargo($idpersonalcargo);

        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $cuposhatl = new \Liquidaciones\CuposAnualesBundle\Entity\CuposHATiposLiquidacion();
        $cuposhatl = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($HAtlCupo);

        $entity->setRefCupoTipoLiquidacion($HAtlCupo);
        $entity->setCuposhatipoliquidacion($cuposhatl);


        // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
        if ($this->cupoCerradoPorId($cuposhatl->getId()) == true) {
            $direccion = "liquidaciones_cuposliquidacion";
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
            );
            return $this->redirect($this->generateUrl($direccion));
        }
        ////////////////////////// FIN  ////////////////////////////////////////////////////

        if (($entity->getMontoTotalCalculado() == 0) || ($entity->getMontoTotalCalculado()== null)) {
            if ($cuposhatl->getCuentas()->getId() == 26) {
                $direccion = "liquidaciones_newhsinvestigacion";
            } else {
                $direccion = "liquidaciones_newhs";
            }
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'El monto total debe ser mayor a cero. '
            );
            return $this->redirect($this->generateUrl($direccion,array('persona'=>$idpersonalcargo)));

        }

        if ($cuposhatl->getCuentas()->getId() != 26) {
            $valorT = round($entity->getMontoTotalCalculado(),2);

            $valorS = round((round($entity->getHsExValorHora(),2) * 1.5 * $entity->getHsExCantSimples()),2);

            $valorD = round((round($entity->getHsExValorHora(),2) * 2 * $entity->getHsExCantDobles()),2);

            $valorSD = round($valorS,2, PHP_ROUND_HALF_DOWN) + round($valorD,2, PHP_ROUND_HALF_DOWN);

            $valorDif = round($valorT,2, PHP_ROUND_HALF_DOWN) - round($valorSD,2, PHP_ROUND_HALF_DOWN);

            if (($valorDif < -0.09) || ($valorDif > 0.09)) {
                $direccion = "liquidaciones_newhs";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El monto total no coincide con las cantidades. '
                );
                return $this->redirect($this->generateUrl($direccion,array('persona'=>$idpersonalcargo)));

            }
        }

        //$usr = $this->get('security.context')->getToken()->getUser();
        //$entity->setUsuaCrea($usr);

        $session = $this->getRequest()->getSession();
        $idcupo = $this->getRequest()->getSession()->get('cupo');
        $TipoBusqueda = $this->getRequest()->getSession()->get('tipobusqueda');
        $modocarga = $this->getRequest()->getSession()->get('modocarga');
        $HAtlCupo = $this->getRequest()->getSession()->get('hatlcupo');
        $persona = $this->getRequest()->getSession()->get('refpersonal');



        //////////////////////////   VALIDACIONES /////////////////////////////////////////////////
        $total = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $cuposhatl->getCupos()->getId())->getResult();
        $totalGastadoCupoMensual = $total[0]["total"];

        if ($cuposhatl->getCuentas()->getId() != 26) {

            if ($cuposhatl->getCupos()->getMonto() - ($totalGastadoCupoMensual + $entity->getMontoTotalCalculado()) < 0) {
                $direccion = "liquidaciones_newhs";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El monto total supera el monto del Cupo Mensual.'
                );
                return $this->redirect($this->generateUrl($direccion,array('persona'=>$idpersonalcargo)));
            }
        }


        ///////////////////////////////////////////////////////////////////////////////////////////



        ///////////////////////////////////////////////////////////////////////////////////////////
        //// nuevo 2020-09-03 ///////////////////////////////////////////////////////////

        //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
        $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$persona.',4';

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $rResult = 0;

        $rResult = $stmt->fetchAll();

        //die(var_dump($rResult));

        $rRegimenHorario = 0;
        $rRegimenHorario = $rResult[0]['RegHorario'];
        /////////////////////////////////////////////////////////////////////////////////
        //////////////////// valido que no supere sus horas /////////////////////////////
        $totalHSV = $em->createQuery('SELECT sum(l.hsExCantSimples) as hssimples,sum(l.hsExCantDobles) as hsdobles FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      JOIN ca.cupos c
                      WHERE (ca.refCuenta not in (1,2,3,4,3,18,18,19) and c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('Anio' => $cuposhatl->getCupos()->getAnio(), 'Mes'=> $cuposhatl->getCupos()->getMes(),'idPersonalC'=> $persona,))->getResult();

        //die(var_dump($totalHS));
        if ($totalHSV != null) {
            $totalHsSimples = $totalHSV[0]["hssimples"] == null ? 0 : $totalHSV[0]["hssimples"];
            $totalHsDobles = $totalHSV[0]["hsdobles"] == null ? 0 : $totalHSV[0]["hsdobles"];
        } else {
            $totalHsSimples = 0;
            $totalHsDobles = 0;
        }

        if ($rRegimenHorario == 30) {
            if ( ($entity->getHsExCantSimples() + $totalHsSimples) > 120) {
                $direccion = "liquidaciones_newhs";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'Las horas simples superan el cupo mensual de 120 horas.'
                );
                return $this->redirect($this->generateUrl($direccion,array('persona'=>$idpersonalcargo)));
            }

            if ( ($entity->getHsExCantDobles() + $totalHsDobles) > 40) {
                $direccion = "liquidaciones_newhs";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'Las horas dobles superan el cupo mensual de 40 horas.'
                );
                return $this->redirect($this->generateUrl($direccion,array('persona'=>$idpersonalcargo)));
            }
        } else {
            if ( ($entity->getHsExCantSimples() + $totalHsSimples) > 80) {
                $direccion = "liquidaciones_newhs";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'Las horas simples superan el cupo mensual de 80 horas.'
                );
                return $this->redirect($this->generateUrl($direccion,array('persona'=>$idpersonalcargo)));
            }

            if ( ($entity->getHsExCantDobles() + $totalHsDobles) > 40) {
                $direccion = "liquidaciones_newhs";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'Las horas dobles superan el cupo mensual de 40 horas.'
                );
                return $this->redirect($this->generateUrl($direccion,array('persona'=>$idpersonalcargo)));
            }
        }

        /////////////////////////////////////////////////////////////////////////////////



        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager("ms_haberes_web");
            $em->persist($entity);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'aviso_ok',
                'Los datos fueron guardados correctamente!'
            );

            return $this->redirect($this->generateUrl('liquidaciones_show', array('id' => $entity->getId())));
        }





        /////////////////////// PARA LOS ACUMULADORES /////////////////
        $total = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $idcupo)->getResult();
        $totalGastadoCupoMensual = $total[0]["total"];
        /*
        $parametersHS = array(
            'idCupo' => $idcupo,
            'idpc' => $persona
        );*/
        $totalHS = $em->createQuery('SELECT sum(l.hsExCantSimples) as hssimples,sum(l.hsExCantDobles) as hsdobles FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  WHERE (ca.refCupo= :idCupo AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('idCupo' => $idcupo,'idPersonalC'=> $persona,))->getResult();

        //die(var_dump($totalHS));
        if ($totalHS != null) {
            $totalHsSimples = $totalHS[0]["hssimples"] == null ? 0 : $totalHS[0]["hssimples"];
            $totalHsDobles = $totalHS[0]["hsdobles"] == null ? 0 : $totalHS[0]["hsdobles"];
        } else {
            $totalHsSimples = 0;
            $totalHsDobles = 0;
        }

        //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
        $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$persona.',4';

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $rResult = 0;

        $rResult = $stmt->fetchAll();

        $session->set('refpersonal', $persona);


        //traigo el valor hora del agente
        $mes = (integer)date('m');
        $anio = (integer)date('Y');

        $sqlVH = "exec haberes.haberes.spTraerValorHoraLiquidaciones ".$mes.",".$anio.",".$persona;

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $rResultVH = 0;

        $rResultVH = $stmt->fetchAll();

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'resultados' => $rResult,
            'cupo' => $idcupo,
            'hacupo' => $HAtlCupo,
            'cupototal'=>$cuposhatl->getCupos()->getMonto(),
            'cupogastado'=>$totalGastadoCupoMensual,
            'valorhora'=>  number_format($rResultVH[0]["valorhora"],2),
            'horassimples'=> $totalHsSimples,
            'horasdobles'=>$totalHsDobles,

            'rg12' => $totalRG12,
            'rg24' => $totalRG24,
            'rg12f'=> $totalRG12F,
            'rg24f'=> $totalRG24F,
            'conceptoValor24hs'=>$concepto24hs,
            'conceptoValor12hs'=>$concepto12hs,
            'conceptoValor24hsFeriado'=>$concepto24hsferiado,
            'conceptoValor12hsFeriado'=>$concepto12hsferiado,
            'diasGuardias'=>$rResultDiasGuardias,
            'diasGuardiasRG'=>$rResultDiasGuardiasRG,
            'mes'=>$mes,
            'anio'=>$anio,
            'fechanovedad'=>$fechaguardia,
            'observacionConcepto'=>$observacionConcepto,
        );

        } else {

            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));

        }
    }





    /**
    * Creates a form to create a Liquidaciones entity.
    *
    * @param Liquidaciones $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Liquidaciones $entity)
    {
        $session = $this->getRequest()->getSession();

        $modocarga = $this->getRequest()->getSession()->get('modocarga');
        $usr = $this->get('security.context')->getToken()->getUser();
        $entity->setUsuaCrea((string)$this->getUser()->getUsername());
        $session = $this->getRequest()->getSession();
        $HAtlCupo = $this->getRequest()->getSession()->get('hatlcupo');
        $idtipoliquidacion = $this->getRequest()->getSession()->get('idtipoliquidacion');

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $cuposhatl = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($HAtlCupo);
        $tipoguardia = array(515=>'12',514=>'24',517=>'12 Hs Feriado',516=>'24 Hs Feriado');
        switch ($modocarga) {
            case 'rg':

                $form = $this->createForm(new \Liquidaciones\CuposAnualesBundle\Form\LiquidacionesType($tipoguardia), $entity, array(
                    'action' => $this->generateUrl('liquidaciones_create'),
                    'method' => 'POST',
                ));

                break;
            case 'horas':
                $idconcepto = $cuposhatl->getCuentas()->getConceptoMS();
                $entity->setIdConcepto($idconcepto);

                $form = $this->createForm(new \Liquidaciones\CuposAnualesBundle\Form\LiquidacionesTypeHS(), $entity, array(
                    'action' => $this->generateUrl('liquidaciones_createhs'),
                    'method' => 'POST',
                ));
                break;
            default:
                $idconcepto = $cuposhatl->getCuentas()->getConceptoMS();
                $entity->setIdConcepto($idconcepto);

                $form = $this->createForm(new \Liquidaciones\CuposAnualesBundle\Form\LiquidacionesTypeM(), $entity, array(
                    'action' => $this->generateUrl('liquidaciones_create'),
                    'method' => 'POST',
                ));
                break;
        }


        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }


	public function devolverVacantesTotales($refCupo)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $vacantes = $em->getRepository("LiquidacionesCuposAnualesBundle:Vacantes")->findOneBy(array("refCupo"=> $refCupo));

        if (count($vacantes) > 0) {
            return $vacantes->getVacantes();
        }

        return 0;

    }


    public function devolverVacantesTotalesHoras($refCupo)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $vacantes = $em->getRepository("LiquidacionesCuposAnualesBundle:Vacantes")->findOneBy(array("refCupo"=> $refCupo));

        if (count($vacantes) > 0) {
            return $vacantes->getVacantes() * 24;
        }

        return 9000000;

    }

    public function devolverTotalHorasRG($refCupo)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $total = $em->createQuery('SELECT sum((CASE WHEN l.idConcepto = 515 THEN 12
                                        WHEN l.idConcepto = 514 THEN 24
                                        WHEN l.idConcepto = 517 THEN 12
                                        WHEN l.idConcepto = 516 THEN 24 ELSE 0 END)) as totalhoras
                                    FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  WHERE l.rGIdPersonalCargo = 0 and ca.refCupo= :idCupo')->setParameter('idCupo', $refCupo)->getResult();

        if (count($total) > 0) {
            return $total[0]["totalhoras"];
        }

        return 0;

    }


    public function devolverTotalHorasRGUpdate($refCupo, $id)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $total = $em->createQuery('SELECT sum((CASE WHEN l.idConcepto = 515 THEN 12
                                        WHEN l.idConcepto = 514 THEN 24
                                        WHEN l.idConcepto = 517 THEN 12
                                        WHEN l.idConcepto = 516 THEN 24 ELSE 0 END)) as totalhoras
                                    FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  WHERE l.id <> '.$id.' and l.rGIdPersonalCargo = 0 and ca.refCupo= :idCupo')->setParameter('idCupo', $refCupo)->getResult();

        if (count($total) > 0) {
            return $total[0]["totalhoras"];
        }

        return 0;

    }


    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/nomina/{refCupo}/{pagina}", name="liquidaciones_nomina")
     * @Method("GET|POST")
     * @Template()
     */
    public function nominaAction($refCupo, $pagina = 1)
    {
        $session = $this->getRequest()->getSession();


        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        //Seguridad
        $securityContext = $this->get('security.context');


        $usr = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $TotalGralLiquidado = 0;

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);

        //die(var_dump($refCupo));


        $cuenta             = $entities->getCuentas()->getModoCarga();
        $idCuenta           = $entities->getCuentas()->getId();
        $HAcupo             = $entities->getId();
        $idtipoliquidacion  = $entities->getCuentas()->getIdTipoLiquidacion();
        $nombrecuenta       = $entities->getCuentas()->getCuenta();
        $anio               = $entities->getCupos()->getAnio();
        $mes                = $entities->getCupos()->getMes();
        $adicional          = $entities->getAdicional();
        $idCuenta           = $entities->getCuentas()->getId();
        $MontoCupo           = $entities->getCupos()->getMonto();


        switch ($idCuenta) {
            case 1:
                if (false === $securityContext->isGranted('ROLE_3')) {
                    $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'No posee permisos para ingresar al contenido solicitado!'
                    );
                    return $this->redirect($this->generateUrl('inicio'));
                }
                break;
            case ($idCuenta == 2 || $idCuenta == 3):
                if (false === $securityContext->isGranted('ROLE_4')) {
                    $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'No posee permisos para ingresar al contenido solicitado!'
                    );
                    return $this->redirect($this->generateUrl('inicio'));
                }
                break;
            case ($idCuenta == 18 || $idCuenta == 19):
                if (false === $securityContext->isGranted('ROLE_5')) {
                    $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'No posee permisos para ingresar al contenido solicitado!'
                    );
                    return $this->redirect($this->generateUrl('inicio'));
                }
                break;
            case ($idCuenta == 5 || $idCuenta == 6 || $idCuenta == 7 || $idCuenta == 8 || $idCuenta == 9 || $idCuenta == 10 || $idCuenta == 11 || $idCuenta == 12 || $idCuenta == 13 || $idCuenta == 14 || $idCuenta == 15 || $idCuenta == 16 || $idCuenta == 17):
                if (false === $securityContext->isGranted('ROLE_6')) {
                    $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'No posee permisos para ingresar al contenido solicitado!'
                    );
                    return $this->redirect($this->generateUrl('inicio'));
                }
                break;
            case ($idCuenta == 20 || $idCuenta == 21):
                if (false === $securityContext->isGranted('ROLE_7')) {
                    $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'No posee permisos para ingresar al contenido solicitado!'
                    );
                    return $this->redirect($this->generateUrl('inicio'));
                }
                break;
            case ($idCuenta == 22 || $idCuenta == 23):
                if (false === $securityContext->isGranted('ROLE_8')) {
                    $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'No posee permisos para ingresar al contenido solicitado!'
                    );
                    return $this->redirect($this->generateUrl('inicio'));
                }
                break;
            case ($idCuenta == 26):
                if (false === $securityContext->isGranted('ROLE_22')) {
                    $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'No posee permisos para ingresar al contenido solicitado!'
                    );
                    return $this->redirect($this->generateUrl('inicio'));
                }
                break;
            default:
                break;
        }


        $session = $this->getRequest()->getSession();
        $session->set('hatlcupo',$HAcupo);
        $session->set('cupo', $refCupo);
        $session->set('tipobusqueda', 'agente');
        $session->set('modocarga', $cuenta);
        $session->set('refpersonalReemplazo', 0);

        $paginaInicio   = 1;
        $paginaFin      = 1;

        $totalRegistros = $em->createQuery("SELECT COUNT(l.idPersonalCargo)
                                FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
        JOIN l.cuposhatipoliquidacion ca
        WHERE ca.id= :idCupo ")->setParameter('idCupo', $refCupo)->getSingleScalarResult();

		$TotalGralLiquidado = $em->createQuery("SELECT sum(l.montoTotalCalculado)
                                FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
        JOIN l.cuposhatipoliquidacion ca
        WHERE ca.refCupo= :idCupo ")->setParameter('idCupo', $entities->getCupos()->getId())->getSingleScalarResult();
        /* PAGINADOR */


        if (($totalRegistros % 15)==0) {
            $paginas = floor($totalRegistros / 15);
        } else {
            $paginas = floor($totalRegistros / 15)+1;
        }

        if ($pagina > $paginas) {
            $pagina = 1;
        }

        if ((15*$pagina) > $totalRegistros) {

            $paginaInicio = 15*($pagina-1);
            $paginaFin    = $totalRegistros;

        } else {


            $paginaInicio = 15*($pagina-1);
            $paginaFin    = (15*$pagina);
        }

        /* FIN del PAGINADOR */

        switch ($cuenta) {
            case 'rg':

                $sqlAgentes = "SELECT top 15
                                t.total,
                                t.idpersonalcargo,
                                t.apyn,
                                t.legajo,
                                t.concepto,
                                ISNULL(RTRIM(t.reemplazado),'VACANTE') as reemplazado,
                                t.fecha,
                                t.cantidad,
                                t.rGIdPersonalCargo as refRGpersonalcargo,
                                t.id
                                from (
                                        select
                                            ROW_NUMBER() OVER(ORDER BY l.id DESC) AS Row,
                                            l.montoTotalCalculado as total,
                                            l.idPersonalCargo,
                                            pp.apellido + ' ' + pp.nombre as apyn,
                                            pp.legajo,
                                            co.nombre as concepto,
                                            ISNULL(RTRIM(ppr.apellido + ' ' + ppr.nombre),'VACANTE') as reemplazado,
                                            l.rGIdPersonalCargo,
                                            l.rGFecha as fecha,
                                            l.rGCantHsGuardia as cantidad,
                                            l.id FROM LiquidacionesWeb.dbo.liquidaciones l
                    INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                    INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                    INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                    LEFT JOIN Haberes.Personal.HAPersonalCargos pcR ON pcR.idPersonalCargo = l.rGIdPersonalCargo
                    LEFT JOIN Haberes.Personal.HAPersonal ppR ON ppR.IdPersona = pcR.IdPersona
                    INNER JOIN Haberes.Haberes.HAAgrupamiento ag ON ag.idagrupamiento = pc.IdAgrupamiento
                    inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                    inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                    inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                    inner join Haberes.Haberes.HAConceptos co ON co.idConcepto = l.idConcepto
                    inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                    inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                    WHERE ca.id= ".$refCupo.") t where t.Row >= ".$paginaInicio;

                //die(var_dump($sqlAgentes));

                $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

                $agentes = $connection ->  prepare($sqlAgentes);
                $agentes -> execute();

                $columns = array(
                        new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                        new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'filter' => 'select', 'selectFrom' => 'query','operatorsVisible'=> false, 'title' => 'Apellido y Nombre','sortable' => false)),
                        new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'operatorsVisible'=> false, 'title' => 'Legajo')),
                        new Column\TextColumn(array('id' => 'concepto', 'field' => 'concepto','filterable' => false, 'source' => true, 'title' => 'Guardia')),
                        new Column\TextColumn(array('id' => 'reemplazado', 'field' => 'reemplazado','filterable' => false, 'source' => true, 'title' => 'Reemplazado')),
                        new Column\DateColumn(array('id' => 'fecha', 'field' => 'fecha', 'source' => true,'filterable' => false, 'title' => 'Fecha')),
                        new Column\NumberColumn(array('id' => 'cantidad', 'field' => 'cantidad', 'source' => true,'filterable' => false, 'title' => 'Cantidad')),
                        new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                        new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                        new Column\NumberColumn(array('id' => 'refRGpersonalcargo', 'field' => 'refRGpersonalcargo', 'source' => true,'filterable' => false,'operatorsVisible'=> false,'visible'=>false, 'title' => 'ID')),
                    );
                break;
            case 'horas':


                $sqlAgentes = "SELECT top 15
                                t.total,
                                t.idpersonalcargo,
                                t.apyn,
                                t.legajo,
                                t.valorhora,
                                t.simples,
                                t.dobles,
                                t.id
                                from (
                                        select
                                            ROW_NUMBER() OVER(ORDER BY l.id DESC) AS Row,
                                            l.montoTotalCalculado as total,
                                            l.idPersonalCargo as idpersonalcargo,
                                            pp.apellido + ' ' + pp.nombre as apyn,
                                            pp.legajo,
                                            l.hsExValorHora as valorhora,
                                            l.hsExCantSimples as simples,
                                            l.hsExCantDobles as dobles,
                                            l.id FROM LiquidacionesWeb.dbo.liquidaciones l
                    INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                    INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                    INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                    inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                    inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                    inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                    inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                    inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                    WHERE ca.id= ".$refCupo.") t where t.Row >= ".$paginaInicio;


                $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

                $agentes = $connection ->  prepare($sqlAgentes);
                $agentes -> execute();


                if ($idCuenta == 26) {
                    $columns = array(
                        new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                        new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'operatorsVisible'=> false,'filter' => 'select', 'selectFrom' => 'query', 'title' => 'Apellido y Nombre')),
                        new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'operatorsVisible'=> false, 'title' => 'Legajo')),
                        new Column\NumberColumn(array('id' => 'valorhora', 'field' => 'valorhora', 'source' => true,'filterable' => false, 'title' => 'Valor Hora')),
                        new Column\NumberColumn(array('id' => 'simples', 'field' => 'simples', 'source' => true,'filterable' => false, 'title' => 'Horas')),
                        new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                        new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                        new Column\NumberColumn(array('id' => 'refRGpersonalcargo', 'field' => 'refRGpersonalcargo', 'source' => true,'filterable' => false,'operatorsVisible'=> false,'visible'=>false, 'title' => 'ID')),
                    );
                } else {
                    $columns = array(
                        new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                        new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'operatorsVisible'=> false,'filter' => 'select', 'selectFrom' => 'query', 'title' => 'Apellido y Nombre')),
                        new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'operatorsVisible'=> false, 'title' => 'Legajo')),
                        new Column\NumberColumn(array('id' => 'valorhora', 'field' => 'valorhora', 'source' => true,'filterable' => false, 'title' => 'Valor Hora')),
                        new Column\NumberColumn(array('id' => 'simples', 'field' => 'simples', 'source' => true,'filterable' => false, 'title' => 'Hs Simples')),
                        new Column\NumberColumn(array('id' => 'dobles', 'field' => 'dobles', 'source' => true,'filterable' => false, 'title' => 'Hs Dobles')),
                        new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                        new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                        new Column\NumberColumn(array('id' => 'refRGpersonalcargo', 'field' => 'refRGpersonalcargo', 'source' => true,'filterable' => false,'operatorsVisible'=> false,'visible'=>false, 'title' => 'ID')),
                    );
                }


                break;
            default:

                $sqlAgentes = "SELECT top 15
                                t.total,
                                t.idpersonalcargo,
                                t.apyn,
                                t.legajo,
                                t.id
                                from (
                                        select
                                            ROW_NUMBER() OVER(ORDER BY l.id DESC) AS Row,
                                            l.montoTotalCalculado as total,
                                            l.idPersonalCargo as idpersonalcargo,
                                            pp.apellido + ' ' + pp.nombre as apyn,
                                            pp.legajo,
                                            l.id FROM LiquidacionesWeb.dbo.liquidaciones l
                    INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                    INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                    INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                    inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                    inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                    inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                    inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                    inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                    WHERE ca.id= ".$refCupo.") t where t.Row >= ".$paginaInicio;


                $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

                $agentes = $connection ->  prepare($sqlAgentes);
                $agentes -> execute();



                $columns = array(
                    new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                    new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true, 'title' => 'Apellido y Nombre')),
                    new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true, 'title' => 'Legajo')),
                    new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                    new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                    new Column\NumberColumn(array('id' => 'refRGpersonalcargo', 'field' => 'refRGpersonalcargo', 'source' => true,'filterable' => false,'operatorsVisible'=> false,'visible'=>false, 'title' => 'ID')),
                );

                break;
        }

        if (isset($agentes)) {
              /*
            if (sizeof($agentes)<1) {
                $agentes = array();
            } else {
                $agentes->fetchAll();
            }
            */
            //$agentes->fetchAll();

            $resultado = $agentes->fetchAll();
            //die(print_r($resultado));
            $source = new Vector($resultado, $columns);

            $source->setId(array('id'));

            $grid = $this->get('grid');

            $grid->setSource($source);

            //$grid->addExport(new MSExportarExcel('Exportar a Excel', 'Cupo: '.$nombrecuenta.' - Fecha: '.$anio.'-'.$mes.' - Adicional:'.$adicional, array('delimiter' => ';')));
            //$grid->addExport(new PHPExcelPDFExport('Exportar a PDF', 'Cupo: '.$nombrecuenta.' - Fecha: '.$anio.'-'.$mes.' - Adicional:'.$adicional, array('delimiter' => ';')));


            switch ($cuenta) {
                case 'rg':

                    $myRowAction = new RowAction('Ver', 'liquidaciones_show', false, '_self', array('class' => 'show'));
                    $myRowAction->setRouteParameters(array('id'));
                    $grid->addRowAction($myRowAction);

                    $myRowAction = new RowAction('Modificar', 'liquidaciones_edit');
                    $myRowAction->setRouteParameters(array('id','refRGpersonalcargo'));
                    $grid->addRowAction($myRowAction);

                    if ($entities->getCupos()->getCupoEstado()->getId() == 2) {
                        $myRowAction = new RowAction('Eliminar', 'liquidaciones_delete');
                        $myRowAction->setRouteParameters(array('id','idcupo'=> $refCupo));
                        $grid->addRowAction($myRowAction);
                    }

                    break;
                case 'horas':

                    $myRowAction = new RowAction('Ver', 'liquidaciones_show', false, '_self', array('class' => 'show'));
                    $myRowAction->setRouteParameters(array('id'));
                    $grid->addRowAction($myRowAction);


                    if ($idCuenta == 26) {
                        $myRowAction = new RowAction('Modificar', 'liquidaciones_edithsinvestigacion');
                    } else {
                        $myRowAction = new RowAction('Modificar', 'liquidaciones_ediths');
                    }
                    $myRowAction->setRouteParameters(array('id'));
                    $grid->addRowAction($myRowAction);

                    if ($entities->getCupos()->getCupoEstado()->getId() == 2) {
                        $myRowAction = new RowAction('Eliminar', 'liquidaciones_delete');
                        $myRowAction->setRouteParameters(array('id','idcupo'=> $refCupo));
                        $grid->addRowAction($myRowAction);
                    }

                    break;

                case 'monto':

                    $myRowAction = new RowAction('Ver', 'liquidaciones_show', false, '_self', array('class' => 'show'));
                    $myRowAction->setRouteParameters(array('id'));
                    $grid->addRowAction($myRowAction);

                    $myRowAction = new RowAction('Modificar', 'liquidaciones_editmonto');
                    $myRowAction->setRouteParameters(array('id'));
                    $grid->addRowAction($myRowAction);

                    if ($entities->getCupos()->getCupoEstado()->getId() == 2) {
                        $myRowAction = new RowAction('Eliminar', 'liquidaciones_delete');
                        $myRowAction->setRouteParameters(array('id','idcupo'=> $refCupo));
                        $grid->addRowAction($myRowAction);
                    }

                    break;
            }

            //$myRowAction2 = new RowAction('Modificar', 'liquidaciones_edit', false, '_self', array('class' => 'edit'));
            //$myRowAction2->setRouteParameters(array('id',null));
            //$source->setRouteParameter(array('id', 'idPersonalCargo'));
            //$grid->addRowAction($myRowAction2);

            $grid->setActionsColumnSize(90);
            //$grid->setLimits(25,50,75);
            //$grid->setLimits(array(5, 10, 15));





        } else {
            $agentes = array(array('Apellido y Nombre' => null,
                                    'Importe' => null));


            $source = new Vector($agentes);

            $grid = $this->get('grid');

            $grid->setSource($source);

            $totalRegistros = 0;
            $paginas = 0;

            $grid->setActionsColumnSize(90);
            //$grid->setLimits(25,50,75);
        }
        $session = $this->getRequest()->getSession();

        $fechasCierre = $em->getRepository("LiquidacionesCuposAnualesBundle:FechaCierre")->findOneBy(array('refCupo'=> $entities->getRefCupo()));

        $FechaHasta = '';
        //die(var_dump($fechasCierre->getFechaHasta()));
        if (($fechasCierre != null) && ($entities->getCupos()->getCupoEstado()->getId() != 4)) {
            if ($entities->getCupos()->getCupoEstado()->getId() == 1) {
                //die(var_dump($fechasCierre));

                $FechaHasta = $fechasCierre->getFechaHasta();

                $habilitaFechaCierre = $this->devolverFechaCierre($entities->getId());

                if ($habilitaFechaCierre == 0) {
                    $direccion = "liquidaciones_cuposliquidacion";
                    $this->getRequest()->getSession()->getFlashBag()->add(
                        'aviso_error',
                        'Sistema no habilitado!!.'
                    );
                    return $this->redirect($this->generateUrl($direccion));
                }
                //die(var_dump($FechaHasta));
            }
        }

        $vacantes = $em->getRepository("LiquidacionesCuposAnualesBundle:Vacantes")->findOneBy(array("refCupo"=> $entities->getRefCupo()));

        $vacantesTotales = $this->devolverVacantesTotales($entities->getRefCupo());
        $vacantesTotalesHs = $this->devolverVacantesTotalesHoras($entities->getRefCupo());
        $horasGastadas = $this->devolverTotalHorasRG($entities->getRefCupo());


        return $grid->getGridResponse('LiquidacionesCuposAnualesBundle:Liquidaciones:nomina.html.twig',
            array('grid'=>$grid,
                  'total'=>$TotalGralLiquidado,
                  'refcupo'=>$refCupo,
                  'paginaActual'=>$pagina,
                  'paginasTotales'=>$paginas,
                  'totalRegistros'=>$totalRegistros,
                  'fechaCierre'=>$FechaHasta,
                  'idCuenta'=>$idCuenta,
                  'MontoCupo'=>$MontoCupo,
                  'modocarga'=> $cuenta,
                  'guia'=> $nombrecuenta." ".$anio."-".$mes." - Adic: ".$adicional,
				  'vacantes' => count($vacantes),
                  'vacantesTotales' => $vacantesTotales,
                  'vacantesTotalesHs' => $vacantesTotalesHs,
                  'horasGastadas' => $horasGastadas,
				));

    }






    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/nominadeuda/{refCupo}/{pagina}", name="liquidaciones_nominadeuda")
     * @Method("GET|POST")
     * @Template()
     */
    public function nominadeudaAction($refCupo, $pagina = 1)
    {
        $session = $this->getRequest()->getSession();


        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        //Seguridad
        $securityContext = $this->get('security.context');


        $usr = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $TotalGralLiquidado = 0;

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);

        //die(var_dump($refCupo));


        $cuenta             = $entities->getCuentas()->getModoCarga();
        $idCuenta           = $entities->getCuentas()->getId();
        $HAcupo             = $entities->getId();
        $idtipoliquidacion  = $entities->getCuentas()->getIdTipoLiquidacion();
        $nombrecuenta       = $entities->getCuentas()->getCuenta();
        $anio               = $entities->getCupos()->getAnio();
        $mes                = $entities->getCupos()->getMes();
        $adicional          = $entities->getAdicional();


        switch ($idCuenta) {
            case 1:
                if (false === $securityContext->isGranted('ROLE_3')) {
                    $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'No posee permisos para ingresar al contenido solicitado!'
                    );
                    return $this->redirect($this->generateUrl('inicio'));
                }
                break;
            case ($idCuenta == 2 || $idCuenta == 3):
                if (false === $securityContext->isGranted('ROLE_4')) {
                    $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'No posee permisos para ingresar al contenido solicitado!'
                    );
                    return $this->redirect($this->generateUrl('inicio'));
                }
                break;
            case ($idCuenta == 18 || $idCuenta == 19):
                if (false === $securityContext->isGranted('ROLE_5')) {
                    $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'No posee permisos para ingresar al contenido solicitado!'
                    );
                    return $this->redirect($this->generateUrl('inicio'));
                }
                break;
            case ($idCuenta == 5 || $idCuenta == 6 || $idCuenta == 7 || $idCuenta == 8 || $idCuenta == 9 || $idCuenta == 10 || $idCuenta == 11 || $idCuenta == 12 || $idCuenta == 13 || $idCuenta == 14 || $idCuenta == 15 || $idCuenta == 16 || $idCuenta == 17):
                if (false === $securityContext->isGranted('ROLE_6')) {
                    $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'No posee permisos para ingresar al contenido solicitado!'
                    );
                    return $this->redirect($this->generateUrl('inicio'));
                }
                break;
            case ($idCuenta == 20 || $idCuenta == 21):
                if (false === $securityContext->isGranted('ROLE_7')) {
                    $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'No posee permisos para ingresar al contenido solicitado!'
                    );
                    return $this->redirect($this->generateUrl('inicio'));
                }
                break;
            case ($idCuenta == 22 || $idCuenta == 23):
                if (false === $securityContext->isGranted('ROLE_8')) {
                    $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'No posee permisos para ingresar al contenido solicitado!'
                    );
                    return $this->redirect($this->generateUrl('inicio'));
                }
                break;
            default:
                break;
        }


        $session = $this->getRequest()->getSession();
        $session->set('hatlcupo',$HAcupo);
        $session->set('cupo', $refCupo);
        $session->set('tipobusqueda', 'agente');
        $session->set('modocarga', $cuenta);


        $paginaInicio   = 1;
        $paginaFin      = 1;

        $totalRegistros = $em->createQuery("SELECT COUNT(l.idPersonalCargo)
                                FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
        JOIN l.cuposhatipoliquidacion ca
        WHERE ca.id= :idCupo ")->setParameter('idCupo', $refCupo)->getSingleScalarResult();

        $TotalGralLiquidado = $em->createQuery("SELECT SUM(l.montoTotalCalculado)
                                FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
        JOIN l.cuposhatipoliquidacion ca
        WHERE ca.id= :idCupo ")->setParameter('idCupo', $refCupo)->getSingleScalarResult();
        /* PAGINADOR */


        if (($totalRegistros % 15)==0) {
            $paginas = floor($totalRegistros / 15);
        } else {
            $paginas = floor($totalRegistros / 15)+1;
        }

        if ($pagina > $paginas) {
            $pagina = 1;
        }

        if ((15*$pagina) > $totalRegistros) {

            $paginaInicio = 15*($pagina-1);
            $paginaFin    = $totalRegistros;

        } else {


            $paginaInicio = 15*($pagina-1);
            $paginaFin    = (15*$pagina);
        }

        /* FIN del PAGINADOR */

        switch ($cuenta) {
            case 'rg':

                $sqlAgentes = "SELECT top 15
                                t.Row,
                                t.total,
                                t.idpersonalcargo,
                                t.apyn,
                                t.legajo,
                                t.concepto,
                                t.reemplazado,
                                t.rGIdPersonalCargo as refRGpersonalcargo,
                                t.fecha,
                                t.cantidad,
                                t.id
                                from (
                                        select
                                            ROW_NUMBER() OVER(ORDER BY l.id DESC) AS Row,
                                            l.montoTotalCalculado as total,
                                            l.idPersonalCargo,
                                            pp.apellido + ' ' + pp.nombre as apyn,
                                            pp.legajo,
                                            co.nombre as concepto,
                                            ISNULL(rtrim(ppr.apellido + ' ' + ppr.nombre),'Vacante') as reemplazado,
                                            l.rGIdPersonalCargo,
                                            l.rGFecha as fecha,
                                            l.rGCantHsGuardia as cantidad,
                                            l.id FROM LiquidacionesWeb.dbo.liquidaciones l
                    INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                    INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                    INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                    LEFT JOIN Haberes.Personal.HAPersonalCargos pcR ON pcR.idPersonalCargo = l.rGIdPersonalCargo
                    LEFT JOIN Haberes.Personal.HAPersonal ppR ON ppR.IdPersona = pcR.IdPersona
                    INNER JOIN Haberes.Haberes.HAAgrupamiento ag ON ag.idagrupamiento = pc.IdAgrupamiento
                    inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                    inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                    inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                    inner join Haberes.Haberes.HAConceptos co ON co.idConcepto = l.idConcepto
                    inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                    inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                    WHERE ca.id= ".$refCupo.") t where t.Row >= ".$paginaInicio;


                $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

                $agentes = $connection ->  prepare($sqlAgentes);
                $agentes -> execute();

                $columns = array(
                        new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                        new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'filter' => 'select', 'selectFrom' => 'query','operatorsVisible'=> false, 'title' => 'Apellido y Nombre','sortable' => false)),
                        new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'operatorsVisible'=> false, 'title' => 'Legajo')),
                        new Column\TextColumn(array('id' => 'concepto', 'field' => 'concepto','filterable' => false, 'source' => true, 'title' => 'Guardia')),
                        new Column\TextColumn(array('id' => 'reemplazado', 'field' => 'reemplazado','filterable' => false, 'source' => true, 'title' => 'Reemplazado')),
                        new Column\DateColumn(array('id' => 'fecha', 'field' => 'fecha', 'source' => true,'filterable' => false, 'title' => 'Fecha')),
                        new Column\NumberColumn(array('id' => 'cantidad', 'field' => 'cantidad', 'source' => true,'filterable' => false, 'title' => 'Cantidad')),
                        new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                        new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                        new Column\NumberColumn(array('id' => 'rGIdPersonalCargo', 'field' => 'rGIdPersonalCargo', 'source' => true,'filterable' => false,'operatorsVisible'=> false,'visible'=>false, 'title' => 'ID')),
                    );
                break;
            case 'horas':


                $sqlAgentes = "SELECT top 15
                                t.Row,
                                t.total,
                                t.idpersonalcargo,
                                t.apyn,
                                t.legajo,
                                t.valorhora,
                                t.simples,
                                t.dobles,
                                t.id
                                from (
                                        select
                                            ROW_NUMBER() OVER(ORDER BY l.id DESC) AS Row,
                                            l.montoTotalCalculado as total,
                                            l.idPersonalCargo as idpersonalcargo,
                                            pp.apellido + ' ' + pp.nombre as apyn,
                                            pp.legajo,
                                            l.hsExValorHora as valorhora,
                                            l.hsExCantSimples as simples,
                                            l.hsExCantDobles as dobles,
                                            l.id FROM LiquidacionesWeb.dbo.liquidaciones l
                    INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                    INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                    INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                    inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                    inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                    inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                    inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                    inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                    WHERE ca.id= ".$refCupo.") t where t.Row >= ".$paginaInicio;


                $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

                $agentes = $connection ->  prepare($sqlAgentes);
                $agentes -> execute();



                $columns = array(
                    new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                    new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'operatorsVisible'=> false,'filter' => 'select', 'selectFrom' => 'query', 'title' => 'Apellido y Nombre')),
                    new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'operatorsVisible'=> false, 'title' => 'Legajo')),
                    new Column\NumberColumn(array('id' => 'valorhora', 'field' => 'valorhora', 'source' => true,'filterable' => false, 'title' => 'Valor Hora')),
                    new Column\NumberColumn(array('id' => 'simples', 'field' => 'simples', 'source' => true,'filterable' => false, 'title' => 'Hs Simples')),
                    new Column\NumberColumn(array('id' => 'dobles', 'field' => 'dobles', 'source' => true,'filterable' => false, 'title' => 'Hs Dobles')),
                    new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                    new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                );
                break;
            default:

                $sqlAgentes = "SELECT top 15
                                t.Row,
                                t.total,
                                t.idpersonalcargo,
                                t.apyn,
                                t.legajo,
                                t.id
                                from (
                                        select
                                            ROW_NUMBER() OVER(ORDER BY l.id DESC) AS Row,
                                            l.montoTotalCalculado as total,
                                            l.idPersonalCargo as idpersonalcargo,
                                            pp.apellido + ' ' + pp.nombre as apyn,
                                            pp.legajo,
                                            l.id FROM LiquidacionesWeb.dbo.liquidaciones l
                    INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                    INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                    INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                    inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                    inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                    inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                    inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                    inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                    WHERE ca.id= ".$refCupo.") t where t.Row >= ".$paginaInicio;


                $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

                $agentes = $connection ->  prepare($sqlAgentes);
                $agentes -> execute();



                $columns = array(
                    new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                    new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true, 'title' => 'Apellido y Nombre')),
                    new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true, 'title' => 'Legajo')),
                    new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                    new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                );

                break;
        }

        if (isset($agentes)) {



            $source = new Vector($agentes, $columns);

            $source->setId(array('id'));

            $grid = $this->get('grid');

            $grid->setSource($source);

            //$grid->addExport(new MSExportarExcel('Exportar a Excel', 'Cupo: '.$nombrecuenta.' - Fecha: '.$anio.'-'.$mes.' - Adicional:'.$adicional, array('delimiter' => ';')));
            //$grid->addExport(new PHPExcelPDFExport('Exportar a PDF', 'Cupo: '.$nombrecuenta.' - Fecha: '.$anio.'-'.$mes.' - Adicional:'.$adicional, array('delimiter' => ';')));



            $myRowAction = new RowAction('Ver', 'liquidaciones_show', false, '_self', array('class' => 'show'));
            $myRowAction->setRouteParameters(array('id'));
            //$source->setRouteParameter(array('id', 'idPersonalCargo'));
            $grid->addRowAction($myRowAction);

            $grid->setActionsColumnSize(90);
            //$grid->setLimits(25,50,75);
            //$grid->setLimits(array(5, 10, 15));





        } else {
            $lstAgentes = array(array('Apellido y Nombre' => null,
                                    'Importe' => null));


            $source = new Vector($lstAgentes);

            $grid = $this->get('grid');

            $grid->setSource($source);

            $totalRegistros = 0;
            $paginas = 0;

            $grid->setActionsColumnSize(90);
            //$grid->setLimits(25,50,75);
        }
        $session = $this->getRequest()->getSession();

        $fechasCierre = $em->getRepository("LiquidacionesCuposAnualesBundle:FechaCierre")->findOneBy(array('refCupo'=> $entities->getRefCupo()));

        $FechaHasta = '';
        //die(var_dump($fechasCierre->getFechaHasta()));
        if ($fechasCierre != null) {
            //die(var_dump($fechasCierre));

            $FechaHasta = $fechasCierre->getFechaHasta();
            //die(var_dump($FechaHasta));
        }

        return $grid->getGridResponse('LiquidacionesCuposAnualesBundle:Liquidaciones:nomina.html.twig',
            array('grid'=>$grid,
                  'total'=>$TotalGralLiquidado,
                  'refcupo'=>$refCupo,
                  'paginaActual'=>$pagina,
                  'paginasTotales'=>$paginas,
                  'totalRegistros'=>$totalRegistros,
                  'fechaCierre'=>$FechaHasta,
                  'guia'=> $nombrecuenta." ".$anio."-".$mes." - Adic: ".$adicional,));

    }


    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/nominabuscar", name="liquidaciones_nominabuscar")
     * @Method("POST")
     * @Template()
     */
    public function nominabuscarAction(Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $session = $this->getRequest()->getSession();
        $TotalGralLiquidado = 0;
        $data = $request->request->all();

        $refCupo = $data["refCupo"];
        $search = str_replace("_search", "", $data["search"]);

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);


        $cuenta = $entities->getCuentas()->getModoCarga();
        $HAcupo = $entities->getId();
        $nombrecuenta = $entities->getCuentas()->getCuenta();
        $idtipoliquidacion = $entities->getCuentas()->getIdTipoLiquidacion();
        $anio               = $entities->getCupos()->getAnio();
        $mes                = $entities->getCupos()->getMes();
        $adicional          = $entities->getAdicional();
        $idCuenta = $entities->getCuentas()->getId();

        $request = $this->getRequest();

        $session->set('hatlcupo',$HAcupo);
        $session->set('cupo', $refCupo);
        $session->set('tipobusqueda', 'agente');
        $session->set('modocarga', $cuenta);

        //die(var_dump($data));

        if (isset($data[$search]["apyn"]["from"][0])) {
            $FiltroApyn = $data[$search]["apyn"]["from"][0];
            $FiltroApyn = ltrim(rtrim(str_replace(' ', '_', $FiltroApyn)));
            $FiltroApyn = str_replace('ñ', 'n', $FiltroApyn);
        } else {
            $FiltroApyn = '';
        }
        if (isset($data[$search]["legajo"]["from"])) {
            $FiltroLegajo = $data[$search]["legajo"]["from"];
            //die(is_numeric($FiltroLegajo));
            if (is_numeric($FiltroLegajo) == false) {
                $FiltroLegajo = '';
            }


        } else {
            $FiltroLegajo = '';
        }

        if (($FiltroApyn != '') && ($FiltroLegajo != '')) {
            $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$FiltroLegajo.',1';
            /*$stmt = $this   ->getDoctrine()
                            ->getManager('odbc_haberes')
                            ->getConnection()
                            ->prepare($sql);

            $rResult = 0;
            $rResult = $stmt->fetch();
            */
            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $rResult = 0;
            $rResult = $stmt->fetchAll();

        } else {
            if (($FiltroApyn != '') && ($FiltroLegajo == '')) {
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$FiltroApyn.',2';

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmt = $conn->prepare($sql);

                $stmt->execute();
                $rResult = 0;
                $rResult = $stmt->fetchAll();
            } else {

                if (($FiltroApyn == '') && ($FiltroLegajo != '')) {
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$FiltroLegajo.',1';

                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();
                } else {
                    $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'Error: El Legajo no puede ser nulo o debe ser numerico o el Apellido y el Nombre deben contener algun dato!'
                    );
                    return $this->redirect($this->generateUrl('liquidaciones_nomina',array('refCupo' => $refCupo)));
                }
            }
        }
        switch ($cuenta) {
            case 'rg':


                if ($rResult != null) {

                    if ($idtipoliquidacion != 40) {

                        foreach ($rResult as $idPersonas) {
                            $agentes = $em->createQuery("SELECT l.montoTotalCalculado as total,
                                                    (CASE WHEN l.idConcepto = 515 THEN '12 Hs'
                                                          WHEN l.idConcepto = 514 THEN '24 Hs'
                                                          WHEN l.idConcepto = 517 THEN '12 Hs Feriado'
                                                          WHEN l.idConcepto = 516 THEN '24 Hs Feriado' ELSE '' END) as concepto,
                                                    l.idPersonalCargo,
                                                    l.idConcepto,
                                                    l.rGIdPersonalCargo,
                                                    l.rGFecha,
                                                    l.rGCantHsGuardia as cantidad,
                                                    l.id FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                            JOIN l.cuposhatipoliquidacion ca
                            WHERE (ca.id= :idCupo and l.idPersonalCargo = :idPersonalCargo) ")->setParameters(array('idCupo'=> $refCupo,'idPersonalCargo'=>$idPersonas['RefPersonalCargo']))->getResult();

                            if ($agentes != null) {
                                break;
                            }
                        }



                        } else {

						    foreach ($rResult as $idPersonas) {
                                $agentes = $em->createQuery("SELECT l.montoTotalCalculado as total,
                                                        (CASE WHEN l.idConcepto = 365 THEN '12 Hs'
                                                              WHEN l.idConcepto = 364 THEN '24 Hs'
                                                              WHEN l.idConcepto = 367 THEN '12 Hs Feriado'
                                                              WHEN l.idConcepto = 366 THEN '24 Hs Feriado' ELSE '' END) as concepto,
                                                        l.idPersonalCargo,
                                                        l.idConcepto,
                                                        l.rGIdPersonalCargo,
                                                        l.rGFecha,
                                                        l.rGCantHsGuardia as cantidad,
                                                        l.id FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                                JOIN l.cuposhatipoliquidacion ca
                                WHERE (ca.id= :idCupo and l.idPersonalCargo = :idPersonalCargo) ")->setParameters(array('idCupo'=> $refCupo,'idPersonalCargo'=>$idPersonas['RefPersonalCargo']))->getResult();

                                if ($agentes != null) {
                                    break;
                                }
                            }

                        }


                    foreach ($agentes as $item) {

                    //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$item['idPersonalCargo'].',4';
                    /*$stmt = $this   ->getDoctrine()
                                    ->getManager('odbc_haberes')
                                    ->getConnection()
                                    ->prepare($sql);

                    $rResult = 0;
                    $rResult = $stmt->fetch();
                    */
                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();

                    //////////////////////////////////////// PARA el reemplazado /////////////////////////////////////////
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.($item['rGIdPersonalCargo']=='' ? 0 : $item['rGIdPersonalCargo']).',4';
                    /*$stmt = $this   ->getDoctrine()
                                    ->getManager('odbc_haberes')
                                    ->getConnection()
                                    ->prepare($sql);

                    $rResultReemp = 0;
                    $rResultReemp = $stmt->fetch();*/

                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResultReemp = 0;
                    $rResultReemp = $stmt->fetchAll();

                    $TotalGralLiquidado = $TotalGralLiquidado + $item['total'];

                    $lstAgentesU = array('id' => $item['id'],
                                    'idpersonalcargo' => $item['idPersonalCargo'],
                                    'apyn'=> $rResult[0]['apyn'],
                                    'legajo'=> $rResult[0]['Legajo'],
                                    'concepto'=>$item['concepto'],
                                    'reemplazado'=> $rResultReemp == null ? '' : $rResultReemp[0]['apyn'],
                                    'fecha'=>$item['rGFecha'],
                                    'cantidad'=>$item['cantidad'],
                                    'total'=>$item['total']);
                    $lstAgentes[] = $lstAgentesU;


                    }
                }
                //var_dump($agentes);
                //var_dump($lstAgentes);

                $columns = array(
                        new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                        new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'filterable' => false, 'title' => 'Apellido y Nombre','sortable' => false)),
                        new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'filterable' => false, 'title' => 'Legajo')),
                        new Column\TextColumn(array('id' => 'concepto', 'field' => 'concepto','filterable' => false, 'source' => true, 'title' => 'Guardia')),
                        new Column\TextColumn(array('id' => 'reemplazado', 'field' => 'reemplazado','filterable' => false, 'source' => true, 'title' => 'Reemplazado')),
                        new Column\DateColumn(array('id' => 'fecha', 'field' => 'fecha', 'source' => true,'filterable' => false, 'title' => 'Fecha')),
                        new Column\NumberColumn(array('id' => 'cantidad', 'field' => 'cantidad', 'source' => true,'filterable' => false, 'title' => 'Cantidad')),
                        new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                        new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                    );
                break;
            case 'horas':
                if ($rResult != null) {
                $agentes = $em->createQuery('SELECT l.montoTotalCalculado as total,
                                            l.idPersonalCargo as idpersonalcargo,
                                            l.hsExValorHora as valorhora,
                                            l.hsExCantSimples as simples,
                                            l.hsExCantDobles as dobles,
                                            l.id FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  WHERE (ca.id= :idCupo and l.idPersonalCargo = :idPersonalCargo) ')->setParameters(array('idCupo'=> $refCupo,'idPersonalCargo'=>$rResult[0]['RefPersonalCargo']))->getResult();



                foreach ($agentes as $item) {

                    //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$item['idpersonalcargo'].',4';
                    /*$stmt = $this   ->getDoctrine()
                                    ->getManager('odbc_haberes')
                                    ->getConnection()
                                    ->prepare($sql);

                    $rResult = 0;
                    $rResult = $stmt->fetch();*/
                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();


                    $lstAgentesU = array('id' => $item['id'],
                                    'idpersonalcargo' => $item['idpersonalcargo'],
                                    'apyn'=> $rResult[0]['apyn'],
                                    'legajo'=> $rResult[0]['Legajo'],
                                    'simples'=>$item['simples'],
                                    'dobles'=>$item['dobles'],
                                    'valorhora'=>$item['valorhora'],
                                    'total'=>$item['total']);
                    $lstAgentes[] = $lstAgentesU;
                }

                }

                $columns = array(
                    new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                    new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'sortable' => false,'filterable' => false, 'title' => 'Apellido y Nombre')),
                    new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'sortable' => false,'filterable' => false, 'title' => 'Legajo')),
                    new Column\NumberColumn(array('id' => 'valorhora', 'field' => 'valorhora', 'source' => true,'sortable' => false, 'filterable' => false,'title' => 'Valor Hora')),
                    new Column\NumberColumn(array('id' => 'simples', 'field' => 'simples', 'source' => true,'sortable' => false,'filterable' => false, 'title' => 'Hs Simples')),
                    new Column\NumberColumn(array('id' => 'dobles', 'field' => 'dobles', 'source' => true,'sortable' => false,'filterable' => false, 'title' => 'Hs Dobles')),
                    new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'sortable' => false,'filterable' => false, 'title' => 'Importe')),
                    new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                );
                break;
            default:
                if ($rResult != null) {
                    $agentes = $em->createQuery('SELECT l.montoTotalCalculado as total,
                                                l.idPersonalCargo,
                                                l.id FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      WHERE (ca.id= :idCupo and l.idPersonalCargo = :idPersonalCargo) ')->setParameters(array('idCupo'=> $refCupo,'idPersonalCargo'=>$rResult[0]['RefPersonalCargo']))->getResult();

                    foreach ($agentes as $item) {
                        //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
                        $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$item['idPersonalCargo'].',4';
                        /*$stmt = $this   ->getDoctrine()
                                        ->getManager('odbc_haberes')
                                        ->getConnection()
                                        ->prepare($sql);

                        $rResult = 0;
                        $rResult = $stmt->fetch();
                        */
                        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                        $stmt = $conn->prepare($sql);

                        $stmt->execute();
                        $rResult = 0;
                        $rResult = $stmt->fetchAll();


                        $lstAgentesU = array('id' => $item['id'],
                                        'idpersonalcargo' => $item['idPersonalCargo'],
                                        'apyn'=> $rResult[0]['apyn'],
                                        'legajo'=> $rResult[0]['Legajo'],
                                        'total'=>$item['total']);
                        $lstAgentes[] = $lstAgentesU;
                    }
                }
                $columns = array(
                    new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                    new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'filterable' => false, 'title' => 'Apellido y Nombre')),
                    new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'filterable' => false, 'title' => 'Legajo')),
                    new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                    new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                );

                break;
        }

        if (isset($lstAgentes)) {
            $source = new Vector($lstAgentes, $columns);

                $source->setId(array('id'));

                $grid = $this->get('grid');

                $grid->setSource($source);

                $myRowAction = new RowAction('Ver', 'liquidaciones_show', false, '_self', array('class' => 'show'));
                $myRowAction->setRouteParameters(array('id'));
                //$source->setRouteParameter(array('id', 'idPersonalCargo'));
                $grid->addRowAction($myRowAction);



                $grid->setActionsColumnSize(90);
                $grid->setLimits(25,50,75);

        } else {

            $this->get("session")->getFlashBag()->add(
                        'aviso_error',
                        'No se encontraron datos!'
                    );

            $lstAgentesU = array(
                                    'apyn'=> '',
                                    'legajo'=> '',
                                    );
                    $lstAgentes[] = $lstAgentesU;

                    $columns = array(

                    new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => false, 'sortable' => false,'filterable' => false, 'title' => 'Apellido y Nombre')),
                    new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => false, 'sortable' => false,'filterable' => false, 'title' => 'Legajo')),
                );
                    /*
            $lstAgentes = array(array('Apellido y Nombre' => null,
                                    'Legajo' => null,
                                    ));
            */
            $source = new Vector($lstAgentes, $columns);

            $grid = $this->get('grid');

            $grid->setSource($source);


            $grid->setActionsColumnSize(90);
            $grid->setLimits(25,50,75);
        }
        $session = $this->getRequest()->getSession();


            return $grid->getGridResponse('LiquidacionesCuposAnualesBundle:Liquidaciones:nominabuscar.html.twig',
                array('grid'=>$grid,'total'=>$TotalGralLiquidado,'refcupo'=>$refCupo,'idCuenta'=>$idCuenta,'guia'=> $nombrecuenta." ".$anio."-".$mes." - Adic: ".$adicional));

    }





    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/autorizaciones/{refCupo}/{pagina}", name="liquidaciones_autorizaciones")
     * @Method("GET")
     * @Template()
     */
    public function autorizacionesAction($refCupo, $pagina = 1)
    {
        $usr = $this->get('security.context')->getToken()->getUser();

        $session = $this->getRequest()->getSession();

        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $total = 0;

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findBy(array("refCupo"=>$refCupo));

        foreach ($entities as $cupo) {
            $cuenta = $cupo->getCuentas()->getModoCarga();
            $HAcupo = $cupo->getId();
            $nombrecuenta = $cupo->getCuentas()->getCuenta();
            $idtipoliquidacion = $cupo->getCuentas()->getIdTipoLiquidacion();
        }

        // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
        if ($this->cupoCerradoPorId($refCupo) == true) {
            $direccion = "liquidaciones_cuposliquidacion";
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
            );
            return $this->redirect($this->generateUrl($direccion));
        }
        ////////////////////////// FIN  ////////////////////////////////////////////////////


        $session = $this->getRequest()->getSession();
        $session->set('hatlcupo',$HAcupo);
        $session->set('cupo', $refCupo);
        $session->set('tipobusqueda', 'agente');
        $session->set('modocarga', $cuenta);

        switch ($cuenta) {
            case 'rg':

                $agentes = $em->createQuery("SELECT l.montoTotalCalculado as total,
                                            l.idConcepto,
                                            l.idPersonalCargo,
                                            l.idConcepto,
                                            l.rGIdPersonalCargo,
                                            l.rGFecha,
                                            l.rGCantHsGuardia as cantidad,
                                            l.id,
                                            l.requiereAutorizacion,
                                            l.usuaAutoriza FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  WHERE ca.id= :idCupo and l.requiereAutorizacion = '1' ")->setParameter('idCupo', $refCupo)->getResult();

                //die(var_dump($agentes));
                $cant = 0;


                    foreach ($agentes as $item) {
                        $cant +=1;
                        if ($idtipoliquidacion != 40) {
                            switch ($item['idConcepto']) {
                                case '515':
                                    $concepto = '12 Hs';
                                    break;
                                case '514':
                                    $concepto = '24 Hs';
                                    break;
                                case '517':
                                    $concepto = '12 Hs Feriado';
                                    break;
                                case '516':
                                    $concepto = '24 Hs Feriado';
                                    break;
                            }
                        } else {
                            switch ($item['idConcepto']) {
                                case '365':
                                    $concepto = '12 Hs';
                                    break;
                                case '364':
                                    $concepto = '24 Hs';
                                    break;
                                case '367':
                                    $concepto = '12 Hs Feriado';
                                    break;
                                case '366':
                                    $concepto = '24 Hs Feriado';
                                    break;
                            }
                        }
                    $rResult = 0;
                    $rResultReemp = 0;
                    //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$item['idPersonalCargo'].',4';
                    /*$stmt = $this   ->getDoctrine()
                                    ->getManager('ms_haberes')
                                    ->getConnection()
                                    ->prepare($sql);


                    $rResult = $stmt->fetchAll();*/
                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();

                    $rResult = $stmt->fetchAll();

                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.($item['rGIdPersonalCargo']=='' ? 0 : $item['rGIdPersonalCargo']).',4';
                    /*$stmt = $this   ->getDoctrine()
                                    ->getManager('ms_haberes')
                                    ->getConnection()
                                    ->prepare($sql);


                    $rResultReemp = $stmt->fetchAll();*/
                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();

                    $rResultReemp = $stmt->fetchAll();

                    $total = $total + $item['total'];

                    $lstAgentesU = array('id' => $item['id'],
                                    'idpersonalcargo' => $item['idPersonalCargo'],
                                    'apyn'=> $rResult[0]['apyn'],
                                    'legajo'=> $rResult[0]['Legajo'],
                                    'concepto'=>$concepto,
                                    'reemplazado'=> $rResultReemp == null ? '' : $rResultReemp[0]['apyn'],
                                    'fecha'=>$item['rGFecha'],
                                    'cantidad'=>$item['cantidad'],
                                    'total'=>$item['total'],
                                    'requireautorizacion'=>$item['requiereAutorizacion'],
                                    'usuarioautoriza'=>$item['usuaAutoriza']);
                    $lstAgentes[] = $lstAgentesU;


                }

                //die(var_dump($cant));

                $columns = array(
                        new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                        new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'filterable' => false, 'title' => 'Apellido y Nombre')),
                        new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'filterable' => false, 'title' => 'Legajo')),
                        new Column\TextColumn(array('id' => 'concepto', 'field' => 'concepto', 'source' => true,'filterable' => false, 'title' => 'Guardia')),
                        new Column\TextColumn(array('id' => 'reemplazado', 'field' => 'reemplazado', 'source' => true,'filterable' => false, 'title' => 'Reemplazado')),
                        new Column\DateColumn(array('id' => 'fecha', 'field' => 'fecha', 'source' => true,'filterable' => false, 'title' => 'Fecha')),
                        new Column\NumberColumn(array('id' => 'cantidad', 'field' => 'cantidad', 'source' => true,'filterable' => false, 'title' => 'Cantidad')),
                        new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                        new Column\BooleanColumn(array('id'=>'requireautorizacion','field'=>'requireautorizacion', 'source'=>true,'filterable' => false, 'title' => 'Require Aut.')),
                        new Column\TextColumn(array('id' => 'usuarioautoriza', 'field' => 'usuarioautoriza', 'source' => true,'filterable' => false, 'title' => 'Usua. Aut.')),
                        new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                    );
                break;
            case 'horas':
                $agentes = $em->createQuery("SELECT l.montoTotalCalculado as total,
                                            l.idPersonalCargo as idpersonalcargo,
                                            l.hsExValorHora as valorhora,
                                            l.hsExCantSimples as simples,
                                            l.hsExCantDobles as dobles,
                                            l.id FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  WHERE ca.id= :idCupo and l.requiereAutorizacion = '1' ")->setParameter('idCupo', $refCupo)->getResult();



                foreach ($agentes as $item) {

                    //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$item['idpersonalcargo'].',4';
                    /*$stmt = $this   ->getDoctrine()
                                    ->getManager('ms_haberes')
                                    ->getConnection()
                                    ->prepare($sql);

                    $rResult = 0;
                    $rResult = $stmt->fetchAll();*/
                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();

                    $total = $total + $item['total'];

                    $lstAgentesU = array('id' => $item['id'],
                                    'idpersonalcargo' => $item['idpersonalcargo'],
                                    'apyn'=> $rResult[0]['apyn'],
                                    'legajo'=> $rResult[0]['Legajo'],
                                    'simples'=>$item['simples'],
                                    'dobles'=>$item['dobles'],
                                    'valorhora'=>$item['valorhora'],
                                    'total'=>$item['total']);
                    $lstAgentes[] = $lstAgentesU;
                }

                $columns = array(
                    new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                    new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'filterable' => false, 'title' => 'Apellido y Nombre')),
                    new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'filterable' => false, 'title' => 'Legajo')),
                    new Column\NumberColumn(array('id' => 'valorhora', 'field' => 'valorhora', 'source' => true,'filterable' => false, 'title' => 'Valor Hora')),
                    new Column\NumberColumn(array('id' => 'simples', 'field' => 'simples', 'source' => true,'filterable' => false, 'title' => 'Hs Simples')),
                    new Column\NumberColumn(array('id' => 'dobles', 'field' => 'dobles', 'source' => true,'filterable' => false, 'title' => 'Hs Dobles')),
                    new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                    new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                );
                break;
            default:
                $agentes = $em->createQuery("SELECT l.montoTotalCalculado as total,
                                            l.idPersonalCargo,
                                            l.id FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  WHERE ca.id= :idCupo and l.requiereAutorizacion = '1' ")->setParameter('idCupo', $refCupo)->getResult();

                foreach ($agentes as $item) {
                    //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$item['idPersonalCargo'].',4';
                    /*$stmt = $this   ->getDoctrine()
                                    ->getManager('ms_haberes')
                                    ->getConnection()
                                    ->prepare($sql);

                    $rResult = 0;
                    $rResult = $stmt->fetchAll();*/
                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();

                    $total = $total + $item['total'];

                    $lstAgentesU = array('id' => $item['id'],
                                    'idpersonalcargo' => $item['idPersonalCargo'],
                                    'apyn'=> $rResult[0]['apyn'],
                                    'legajo'=> $rResult[0]['Legajo'],
                                    'total'=>$item['total']);
                    $lstAgentes[] = $lstAgentesU;
                }

                $columns = array(
                    new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                    new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'filterable' => false, 'title' => 'Apellido y Nombre')),
                    new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'filterable' => false, 'title' => 'Legajo')),
                    new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                    new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                );

                break;
        }

        if (isset($lstAgentes)) {

            /* PAGINADOR */
            $totalRegistros = count($lstAgentes);

            if (($totalRegistros % 5)==0) {
                $paginas = floor($totalRegistros / 5);
            } else {
                $paginas = floor($totalRegistros / 5)+1;
            }

            if ($pagina > $paginas) {
                $pagina = 1;
            }

            if ((5*$pagina) > $totalRegistros) {
                for ($k=(5*($pagina-1)); $k<$totalRegistros; $k++) {
                    $lstAgentesAux[] = $lstAgentes[$k];
                }

            } else {
                for ($k=(5*($pagina-1)); $k<(5*$pagina); $k++) {
                    $lstAgentesAux[] = $lstAgentes[$k];
                }
            }

            /* FIN del PAGINADOR */

            $source = new Vector($lstAgentes, $columns);

                $source->setId(array('id'));

                $grid = $this->get('grid');

                $grid->setSource($source);




                $myRowAction = new RowAction('Autorizar', 'liquidaciones_autorizarCarga', false, '_self', array('class' => 'show'));
                $myRowAction->setRouteParameters(array('id','refCupo'=> $refCupo));
                //$source->setRouteParameter(array('id', 'idPersonalCargo'));
                $grid->addRowAction($myRowAction);

                $grid->setActionsColumnSize(90);
                $grid->setLimits(25,50,75);

        } else {
            $lstAgentes = array(array('Apellido y Nombre' => null,
                                    'Importe' => null));
            $source = new Vector($lstAgentes);

            $grid = $this->get('grid');

            $grid->setSource($source);
            $paginas = 0;
            $totalRegistros = 0;

            $grid->setActionsColumnSize(90);
            $grid->setLimits(25,50,75);
        }
        $session = $this->getRequest()->getSession();


        return $grid->getGridResponse('LiquidacionesCuposAnualesBundle:Liquidaciones:autorizaciones.html.twig',
                array('grid'=>$grid,'refcupo'=>$refCupo,'total'=>$total,'paginaActual'=>$pagina,'paginasTotales'=>$paginas,'totalRegistros'=>$totalRegistros));
        /*
        return array(
            'entity' => $lstAgentes,
            'cuenta' => $nombrecuenta,
            //'form'   => $form->createView(),
            //'entity'  => $refCupo,
        );*/
    }


    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/buscarpersona/", name="liquidaciones_buscarpersona")
     * @Method("POST")
     * @Template()
     */
    public function buscarpersonaAction(Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        //Seguridad
        $securityContext = $this->get('security.context');

        $data = $request->request->all();

        $refCupo = $data['refcupo'];

        $fechanovedad = $data['liquidaciones_cuposanualesbundle_liquidaciones_rGFecha'];

        if ((true === $securityContext->isGranted('ROLE_2')) ||
           (true === $securityContext->isGranted('ROLE_10')) ||
           (true === $securityContext->isGranted('ROLE_11')) ||
           (true === $securityContext->isGranted('ROLE_12')) ||
           (true === $securityContext->isGranted('ROLE_13')) ||
           (true === $securityContext->isGranted('ROLE_14')) ||
           (true === $securityContext->isGranted('ROLE_16')) ||
           (true === $securityContext->isGranted('ROLE_20')) ||
           (true === $securityContext->isGranted('ROLE_22')) ||
           (true === $securityContext->isGranted('ROLE_17'))) {

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);

        // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
        if ($this->cupoCerradoPorId($refCupo) == true) {
            $direccion = "liquidaciones_cuposliquidacion";
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
            );
            return $this->redirect($this->generateUrl($direccion));
        }
        ////////////////////////// FIN  ////////////////////////////////////////////////////



        $cuenta             = $entities->getCuentas()->getModoCarga();
        $HAcupo             = $entities->getId();
        $nombre             = $entities->getCuentas()->getCuenta();
        $anio               = $entities->getCupos()->getAnio();
        $mes                = $entities->getCupos()->getMes();
        $adicional          = $entities->getAdicional();
        $idtipoliquidacion  = $entities->getCuentas()->getIdTipoLiquidacion();



        $session = $this->getRequest()->getSession();
        $session->set('hatlcupo',$HAcupo);
        $session->set('cupo', $refCupo);
        $session->set('tipobusqueda', 'agente');
        $session->set('modocarga', $cuenta);
        $session->set('idtipoliquidacion', $idtipoliquidacion);
        $session->set('reemplazo', 0);
        $session->set('fechanovedad', $fechanovedad);

        return array(
            //'entity' => $entity,
            //'form'   => $form->createView(),
            'nombre'  => $nombre,
            'idCupo'  => $refCupo,
			'modocarga' => $cuenta,
            'guia'=> $nombre." ".$anio."-".$mes." - Adic: ".$adicional,
            'fechanovedad'=> $fechanovedad,
        );

        } else {

           $direccion = "liquidaciones_cuposliquidacion";
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );
            return $this->redirect($this->generateUrl($direccion));
        }
    }


    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/buscarpersonadeuda/", name="liquidaciones_buscarpersonadeuda")
     * @Method("POST")
     * @Template()
     */
    public function buscarpersonadeudaAction(Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        //Seguridad
        $securityContext = $this->get('security.context');


        $data = $request->request->all();

        $refCupo = $data['refcupo'];

        $fechanovedad = $data['liquidaciones_cuposanualesbundle_liquidaciones_rGFecha'];


        if ((true === $securityContext->isGranted('ROLE_2')) ||
           (true === $securityContext->isGranted('ROLE_10')) ||
           (true === $securityContext->isGranted('ROLE_11')) ||
           (true === $securityContext->isGranted('ROLE_12')) ||
           (true === $securityContext->isGranted('ROLE_13')) ||
           (true === $securityContext->isGranted('ROLE_14')) ||
           (true === $securityContext->isGranted('ROLE_16')) ||
           (true === $securityContext->isGranted('ROLE_20')) ||
           (true === $securityContext->isGranted('ROLE_17'))) {

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);

        // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
        if ($this->cupoCerradoPorId($refCupo) == true) {
            $direccion = "liquidaciones_cuposliquidacion";
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
            );
            return $this->redirect($this->generateUrl($direccion));
        }
        ////////////////////////// FIN  ////////////////////////////////////////////////////



        $cuenta             = $entities->getCuentas()->getModoCarga();
        $HAcupo             = $entities->getId();
        $nombre             = $entities->getCuentas()->getCuenta();
        $anio               = $entities->getCupos()->getAnio();
        $mes                = $entities->getCupos()->getMes();
        $adicional          = $entities->getAdicional();
        $idtipoliquidacion  = $entities->getCuentas()->getIdTipoLiquidacion();



        $session = $this->getRequest()->getSession();
        $session->set('hatlcupo',$HAcupo);
        $session->set('cupo', $refCupo);
        $session->set('tipobusqueda', 'agente');
        $session->set('modocarga', $cuenta);
        $session->set('idtipoliquidacion', $idtipoliquidacion);
        $session->set('reemplazo', 0);

        $session->set('fechanovedad', $fechanovedad);

        return array(
            //'entity' => $entity,
            //'form'   => $form->createView(),
            'nombre'  => $nombre,
            'idCupo'  => $refCupo,
            'guia'=> $nombre." ".$anio."-".$mes." - Adic: ".$adicional,
            'fechanovedad'=> $fechanovedad,
        );

        } else {

           $direccion = "liquidaciones_cuposliquidacion";
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );
            return $this->redirect($this->generateUrl($direccion));
        }
    }



    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/buscarhistoricopersona/", name="liquidaciones_buscarhistoricopersona")
     * @Method("GET")
     * @Template()
     */
    public function buscarhistoricopersonaAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_2')) ||
           (true === $securityContext->isGranted('ROLE_10')) ||
           (true === $securityContext->isGranted('ROLE_11')) ||
           (true === $securityContext->isGranted('ROLE_12')) ||
           (true === $securityContext->isGranted('ROLE_13')) ||
           (true === $securityContext->isGranted('ROLE_14')) ||
           (true === $securityContext->isGranted('ROLE_16')) ||
           (true === $securityContext->isGranted('ROLE_20')) ||
           (true === $securityContext->isGranted('ROLE_22')) ||
           (true === $securityContext->isGranted('ROLE_17'))) {

        return array(

        );

        } else {

           $direccion = "liquidaciones_cuposliquidacion";
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );
            return $this->redirect($this->generateUrl($direccion));
        }
    }



    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/buscarreemplazante/{refCupo}/{fechanovedad}", name="liquidaciones_buscarreemplazante")
     * @Method("GET")
     * @Template()
     */
    public function buscarreemplazanteAction($refCupo,$fechanovedad=null)
    {
        $usr = $this->get('security.context')->getToken()->getUser();


        // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
        if ($this->cupoCerradoPorId($refCupo) == true) {
            $direccion = "liquidaciones_cuposliquidacion";
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
            );
            return $this->redirect($this->generateUrl($direccion));
        }
        ////////////////////////// FIN  ////////////////////////////////////////////////////

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);

        $session = $this->getRequest()->getSession();
        $session->set('cupo', $refCupo);
        $session->set('tipobusqueda', 'reemplazante');
        $editar = $this->getRequest()->getSession()->get('editar');
        $session->set('reemplazo', 1);
        $session->set('fechanovedad', $fechanovedad);


        $nombre = $entities->getCuentas()->getCuenta();
        $idCuenta = $entities->getCuentas()->getId();
            //$cuenta = $cupo->getCuentas()->getModoCarga();


        if ($editar == true) {
            $ruta = "editar";
            $ideditar = $this->getRequest()->getSession()->get('ideditar');
        } else {
            $ruta = "ingresar";
            $ideditar = 0;
        }
        return array(
            'persona' => (integer)$this->getRequest()->getSession()->get('personal'),
            'personarg'=>(integer)$this->getRequest()->getSession()->get('refpersonalReemplazo'),
            'nombre'  => $nombre,
            'ruta' => $ruta,
            'ideditar'=> $ideditar,
            'reemplazo'=>1,
            'idCuenta'=>$idCuenta,
            'fechanovedad'=>$fechanovedad,
        );
    }



    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/quitarreemplazado/{persona}/{deuda}/{id}", name="liquidaciones_quitarreemplazado")
     * @Method("GET")
     * @Template()
     */
    public function quitarreemplazadoAction($persona, $deuda, $id=null)
    {
        $usr = $this->get('security.context')->getToken()->getUser();


        $session = $this->getRequest()->getSession();
        $session->set('refpersonalReemplazo', 0);

        if ($deuda == 1) {
            return $this->redirect($this->generateUrl('liquidaciones_newdeuda',array('persona' => $persona)));
        } else {
            if ($deuda == 2) {
                return $this->redirect($this->generateUrl('liquidaciones_edit',array('id' => $id,'refRGpersonalcargo'=>0)));
            }
        }

        return $this->redirect($this->generateUrl('liquidaciones_new',array('persona' => $persona, 'personarg'=>0,'fechaguardia'=>$session->get('fechanovedad'))));
    }




    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/new/{persona}/{personarg}/{fechaguardia}", name="liquidaciones_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($persona,$personarg, $fechaguardia=null)
    {

    	//Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_9')) ||
            (true === $securityContext->isGranted('ROLE_10')) || (true === $securityContext->isGranted('ROLE_11')) ||
            (true === $securityContext->isGranted('ROLE_12')) || (true === $securityContext->isGranted('ROLE_13')) ||
            (true === $securityContext->isGranted('ROLE_14')) || (true === $securityContext->isGranted('ROLE_16')) ||
            (true === $securityContext->isGranted('ROLE_20')) || (true === $securityContext->isGranted('ROLE_17'))
                ){




                $idcupo = $this->getRequest()->getSession()->get('cupo');
                $TipoBusqueda = $this->getRequest()->getSession()->get('tipobusqueda');
                $HAtlCupo = $this->getRequest()->getSession()->get('hatlcupo');
                $idtipoliquidacion = $this->getRequest()->getSession()->get('idtipoliquidacion');
                $modocarga = $this->getRequest()->getSession()->get('modocarga');
                $reemplazo = $this->getRequest()->getSession()->get('reemplazo');

                $session->set('editar', false);

                $fechanovedad = $this->getRequest()->getSession()->get('fechanovedad');


                ////////////////////////// FIN  ////////////////////////////////////////////////////

                /***************        VERIFICO SI ESTOY DENTRO DE LAS FECHAS DE CIERRE ***********************/
                $habilitaFechaCierre = $this->devolverFechaCierre($idcupo);

                if ($habilitaFechaCierre == 0) {
                    $direccion = "liquidaciones_cuposliquidacion";
                    $this->getRequest()->getSession()->getFlashBag()->add(
                        'aviso_error',
                        'Sistema no habilitado!!.'
                    );
                    return $this->redirect($this->generateUrl($direccion));
                }
                /********************         FIN                 **********************************************/


                $em =  $this->getDoctrine()->getManager("ms_haberes_web");

            $HACupos    = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($idcupo);


            if ($HACupos->getCupos()->getCupoestado()->getId() == 4) {
                    // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
                    if ($this->cupoCerradoPorId($idcupo) == true) {
                        $direccion = "liquidaciones_cuposliquidacion";
                        $this->getRequest()->getSession()->getFlashBag()->add(
                            'aviso_error',
                            'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
                        );
                        return $this->redirect($this->generateUrl($direccion));
                    }
                }



            $rResult = 0;
            $rResultReemplazo = 0;
            $rResultDiasGuardias = 0;
            $rResultDiasGuardiasRG = 0;



            if ($persona == 0) {
                $persona = $this->getRequest()->getSession()->get('personal');
            }

            if ($TipoBusqueda == 'agente') {
                $session->set('personal', $persona);
                $personalcargo = $this->getRequest()->getSession()->get('personal');
                //Vuelvo a buscar a la persona para trearme losd atos de esta/////////////////
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$personalcargo.',4';
                /*$stmt = $this   ->getDoctrine()
                                ->getManager('odbc_haberes')
                                ->getConnection()
                                ->prepare($sql);
                $rResult = $stmt->fetch();*/

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmt = $conn->prepare($sql);

                $stmt->execute();
                //$rResult = 0;
                $rResult = $stmt->fetchAll();

            } else {
                 if ($personarg != 0) {
                    $session->set('refpersonalReemplazo', $personarg);
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$this->getRequest()->getSession()->get('refpersonalReemplazo').',4';

                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResultReemplazo = 0;
                    $rResultReemplazo = $stmt->fetchAll();


                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$this->getRequest()->getSession()->get('personal').',4';

                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();


                 } else {
                    $session->set('personal', $persona);

                    //Vuelvo a buscar a la persona para trearme losd atos de esta/////////////////
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$persona.',4';

                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();
                 }
            }



            /////////////////////// PARA LOS ACUMULADORES /////////////////
            $Fecha = $em->createQuery('SELECT c.anio, c.mes, c.monto FROM LiquidacionesCuposAnualesBundle:Cupos c
                      WHERE c.id = :idCupo
                      GROUP BY c.anio, c.mes, c.monto')->setParameter('idCupo', $HACupos->getCupos()->getId())->getResult();
            $anio = $Fecha[0]["anio"];
            $mes = $Fecha[0]["mes"];


            $total = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $HACupos->getCupos()->getId())->getResult();

            if ($total != null) {
                $totalGastadoCupoMensual = $total[0]["total"] == 0 ? 0 : $total[0]["total"];
            } else {
                $totalGastadoCupoMensual = 0;
            }


            if ($idtipoliquidacion != 40) {


              	//TRAIGO EL VALOR DE LA GUARDIA /////////////
              	$fechanovedadCorta = $mes.'/'.$anio;
		            $em = $this->getDoctrine()->getManager("ms_haberes_web");
		            $query = $em->createQuery(
		                "SELECT cc.monto
		                   FROM LiquidacionesHaberesBundle:HAConceptosValor cc
		                  WHERE cc.refConcepto in (514,515,516,517) and '".date("Y-d-m", strtotime("01/".$fechanovedadCorta))."' >= cc.vigDesde and '".date("Y-d-m", strtotime("01/".$fechanovedadCorta))."' <= cc.vigHasta
		                  order by cc.refConcepto"
		            );

		            $conceptosValor = $query->getResult();

		            $concepto24hs = $conceptosValor[0]['monto'];
		            $concepto12hs = $conceptosValor[1]['monto'];
		            $concepto24hsferiado = $conceptosValor[2]['monto'];
		            $concepto12hsferiado = $conceptosValor[3]['monto'];
                /////////////////////////////////////////////

                $totalRG = $em->createQuery('SELECT
                            sum((case when l.idConcepto = 515 then l.rGCantHsGuardia else 0 end)) as docehs,
                            sum((case when l.idConcepto = 514 then l.rGCantHsGuardia else 0 end)) as veinticuatrohs,
                            sum((case when l.idConcepto = 517 then l.rGCantHsGuardia else 0 end)) as docehsferiado,
                            sum((case when l.idConcepto = 516 then l.rGCantHsGuardia else 0 end)) as veinticutrohsferiado
                        FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      JOIN ca.cupos c
                      WHERE (c.RefCupoEstado = 2 and ca.refCuenta <> 24 and c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('Anio' => $anio, 'Mes'=> $mes,'idPersonalC'=> $persona,))->getResult();
            } else {

                //TRAIGO EL VALOR DE LA GUARDIA /////////////

              	$fechanovedadCorta = $mes.'/'.$anio;
		            $em = $this->getDoctrine()->getManager("ms_haberes_web");
		            $query = $em->createQuery(
		                "SELECT cc.monto
		                   FROM LiquidacionesHaberesBundle:HAConceptosValor cc
		                  WHERE cc.refConcepto in (364,365,366,367) and '".date("Y-d-m", strtotime("01/".$fechanovedadCorta))."' >= cc.vigDesde and '".date("Y-d-m", strtotime("01/".$fechanovedadCorta))."' <= cc.vigHasta
		                  order by cc.refConcepto"
		            );

		            $conceptosValor = $query->getResult();

		            $concepto24hs = $conceptosValor[0]['monto'];
		            $concepto12hs = $conceptosValor[1]['monto'];
		            $concepto24hsferiado = $conceptosValor[2]['monto'];
		            $concepto12hsferiado = $conceptosValor[3]['monto'];
                /////////////////////////////////////////////

                $totalRG = $em->createQuery('SELECT
                            sum((case when l.idConcepto = 365 then l.rGCantHsGuardia else 0 end)) as docehs,
                            sum((case when l.idConcepto = 364 then l.rGCantHsGuardia else 0 end)) as veinticuatrohs,
                            sum((case when l.idConcepto = 367 then l.rGCantHsGuardia else 0 end)) as docehsferiado,
                            sum((case when l.idConcepto = 366 then l.rGCantHsGuardia else 0 end)) as veinticutrohsferiado
                        FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      JOIN ca.cupos c
                      WHERE (c.RefCupoEstado = 2 and c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('Anio' => $anio, 'Mes'=> $mes,'idPersonalC'=> $persona,))->getResult();

            }


            if ($totalRG != null) {
                $totalRG12 = $totalRG[0]["docehs"] == null ? 0 : $totalRG[0]["docehs"];
                $totalRG24 = $totalRG[0]["veinticuatrohs"] == null ? 0 : $totalRG[0]["veinticuatrohs"];
                $totalRG12F = $totalRG[0]["docehsferiado"] == null ? 0 : $totalRG[0]["docehsferiado"];
                $totalRG24F = $totalRG[0]["veinticutrohsferiado"] == null ? 0 : $totalRG[0]["veinticutrohsferiado"];

            } else {
                $totalRG12 = 0;
                $totalRG24 = 0;
                $totalRG12F = 0;
                $totalRG24F = 0;
            }

            //////////////////////  FIN ///////////////////////////////////

            $entity = new Liquidaciones();
            /////   Verifico si la fecha es un feriado   //////////////////
            $sqlFeriados = "EXEC haberes.web.spTraerFeriadoPorFecha_Dependencia '".date('Y-m-d',  strtotime($fechanovedad))."',".$HACupos->getCupos()->getIdDependencia();

            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmtFeriados = $conn->prepare($sqlFeriados);

            $stmtFeriados->execute();
            $rResultFeriados = 0;
            $rResultFeriados = $stmtFeriados->fetchAll();
            //die(var_dump($rResultFeriados));
            //////            FIN                        ///////////////////


            /////   Verifico la cantidad de horas que me quedan segun las guardias del agente y del reemplazado //////
            $sqlSaldoHoras = 'EXEC haberes.haberes.spValidaReemplazoMismoDiaSemana '.$this->getRequest()->getSession()->get('refpersonalReemplazo').','.$persona.','.date('N',  strtotime($fechanovedad)).",'".date('Y-m-d',  strtotime($fechanovedad))."'";

            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmtSaldoHoras = $conn->prepare($sqlSaldoHoras);

            $stmtSaldoHoras->execute();
            $rResultSaldoHoras = 0;
            $rResultSaldoHoras = $stmtSaldoHoras->fetchAll();
            //die(var_dump($rResultSaldoHoras[0]['computed']));
            $valores = explode("/", $rResultSaldoHoras[0]['computed']);

            $observacionConcepto = '';
            $tipoguardia = array(0=>'No posee horas');
            if ($valores[0]==0) {
                $observacionConcepto = $valores[1];
            } else {

                if ((date('N',  strtotime($fechanovedad)) == 6) || (date('N',  strtotime($fechanovedad)) == 7) || ($rResultFeriados[0]['computed'] == 1)) {
                    switch (abs($valores[1])) {
                        case 24:
                            $tipoguardia = array(517=>'12 Hs Feriado',516=>'24 Hs Feriado');
                            $observacionConcepto = "Horas Disponibles en fecha elegida: 24 Hs Feriado";
                            break;
                        case 12:
                           $tipoguardia = array(517=>'12 Hs Feriado');
                           $observacionConcepto = "Horas Disponibles en fecha elegida: 12 Hs Feriado";
                            break;
                        case 0:
                            $tipoguardia = array(517=>'12 Hs Feriado',516=>'24 Hs Feriado');
                            $observacionConcepto = "Horas Disponibles en fecha elegida: 24 Hs Feriado";

                            break;
                        default:
                            $tipoguardia = array(0=>'No posee horas');
                            $observacionConcepto = "Horas Disponibles en fecha elegida: No posee horas";
                            break;
                    }
                } else {
                    switch (abs($valores[1])) {
                        case 24:
                            $tipoguardia = array(515=>'12 Hs',514=>'24 Hs');
                            $observacionConcepto = "Horas Disponibles en fecha elegida: 24 Hs";
                            break;
                        case 12:
                            $tipoguardia = array(515=>'12 Hs');
                            $observacionConcepto = "Horas Disponibles en fecha elegida: 12 Hs";
                            break;
                        case 0:
                            $tipoguardia = array(515=>'12 Hs',514=>'24 Hs');
                            $observacionConcepto = "Horas Disponibles en fecha elegida: 24 Hs";
                            break;
                        default:
                            $tipoguardia = array(0=>'No posee horas');
                            $observacionConcepto = "Horas Disponibles en fecha elegida: No posee horas";
                            break;
                    }
                }

            }
            // RG invalido: Hace guardia ese dia

            // Solo puede 12 horas

            //die(var_dump($rResultSaldoHoras));

            /////                   FIN                             ////////////////////////////////

            $form   = $this->createForm(new \Liquidaciones\CuposAnualesBundle\Form\LiquidacionesType($tipoguardia), $entity);


            if ($personarg == $persona) {
                //die(var_dump($persona));
                $direccion = "liquidaciones_buscarreemplazante";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'Selecciono el mismo agente para el reemplazo. '
                );
                $session->set('refpersonalReemplazo', 0);
                return $this->redirect($this->generateUrl($direccion,array('refCupo'=>$idcupo)));
            }


            //////          Traigo los dias de Guardi del Agente  /////////////////
            $sqlDiasGuardias = 'EXEC haberes.haberes.spDiasGuardiaWeb '.$persona;

            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmtDiasGuardias = $conn->prepare($sqlDiasGuardias);

            $stmtDiasGuardias->execute();
            $rResultDiasGuardias = 0;
            $rResultDiasGuardias = $stmtDiasGuardias->fetchAll();
            //die(var_dump($rResultDiasGuardias));
            /////                  FIN                            /////////////////


            //////          Traigo los dias de Guardi del Agente  /////////////////
            if (($this->getRequest()->getSession()->get('refpersonalReemplazo')!=0)) {
                $sqlDiasGuardiasRG = 'EXEC haberes.haberes.spDiasGuardiaWeb '.$personarg;
                /*$stmt = $this   ->getDoctrine()
                                ->getManager('odbc_haberes')
                                ->getConnection()
                                ->prepare($sql);
                $rResultReemplazo = $stmt->fetch();
                */
                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmtDiasGuardiasRG = $conn->prepare($sqlDiasGuardiasRG);

                $stmtDiasGuardiasRG->execute();
                $rResultDiasGuardiasRG = 0;
                $rResultDiasGuardiasRG = $stmtDiasGuardiasRG->fetchAll();
            }
            /////                  FIN                            /////////////////


            return array(

                'entity' => $entity,
                'form'   => $form->createView(),
                'resultados' => $rResult,
                'resultadosReemplazo' => $rResultReemplazo,
                'cupo' => $idcupo,
                'hacupo' => $HAtlCupo,
                'cupototal'=>$HACupos->getCupos()->getMonto(),
                'cupogastado'=>$totalGastadoCupoMensual,
                'rg12' => $totalRG12,
                'rg24' => $totalRG24,
                'rg12f'=> $totalRG12F,
                'rg24f'=> $totalRG24F,
                'conceptoValor24hs'=>$concepto24hs,
                'conceptoValor12hs'=>$concepto12hs,
                'conceptoValor24hsFeriado'=>$concepto24hsferiado,
                'conceptoValor12hsFeriado'=>$concepto12hsferiado,
                'diasGuardias'=>$rResultDiasGuardias,
                'diasGuardiasRG'=>$rResultDiasGuardiasRG,
                'mes'=>substr('0'.$mes, -2),
                'anio'=>$anio,
                'fechanovedad'=> str_replace('-','/',$fechanovedad),
                'fechanovedadaux'=> $fechaguardia,
                'observacionConcepto'=>$observacionConcepto,
                /*
                'cupo'=>12,
                */
            );

				        } else {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));

        }
    }



    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/newdeuda/{persona}", name="liquidaciones_newdeuda")
     * @Method("GET")
     * @Template()
     */
    public function newdeudaAction($persona)
    {

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_9')) ||
            (true === $securityContext->isGranted('ROLE_10')) || (true === $securityContext->isGranted('ROLE_11')) ||
            (true === $securityContext->isGranted('ROLE_12')) || (true === $securityContext->isGranted('ROLE_13')) ||
            (true === $securityContext->isGranted('ROLE_14')) || (true === $securityContext->isGranted('ROLE_16')) ||
            (true === $securityContext->isGranted('ROLE_20')) || (true === $securityContext->isGranted('ROLE_17'))
                ){


            $idcupo = $this->getRequest()->getSession()->get('cupo');
            $TipoBusqueda = $this->getRequest()->getSession()->get('tipobusqueda');
            $HAtlCupo = $this->getRequest()->getSession()->get('hatlcupo');
            $idtipoliquidacion = $this->getRequest()->getSession()->get('idtipoliquidacion');
            $modocarga = $this->getRequest()->getSession()->get('modocarga');
            $reemplazo = $this->getRequest()->getSession()->get('reemplazo');

            $fechanovedad = $this->getRequest()->getSession()->get('fechanovedad');

            $session->set('editar', false);



            // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
            if ($this->cupoCerradoPorId($idcupo) == true) {
                $direccion = "liquidaciones_cuposliquidacion";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
                );
                return $this->redirect($this->generateUrl($direccion));
            }
            ////////////////////////// FIN  ////////////////////////////////////////////////////

            /***************        VERIFICO SI ESTOY DENTRO DE LAS FECHAS DE CIERRE ***********************/
            $habilitaFechaCierre = $this->devolverFechaCierre($idcupo);

            if ($habilitaFechaCierre == 0) {
                $direccion = "liquidaciones_cuposliquidacion";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'Sistema no habilitado!!.'
                );
                return $this->redirect($this->generateUrl($direccion));
            }
            /********************         FIN                 **********************************************/


            $em =  $this->getDoctrine()->getManager("ms_haberes_web");

            $HACupos    = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($idcupo);

            $em =  $this->getDoctrine()->getManager("ms_haberes_web");

            $novedades   =  $em->getRepository('LiquidacionesParteNovedadesBundle:Novedad')->findAll();

            $rResult = 0;
            $rResultReemplazo = 0;
            $rResultDiasGuardias = 0;
            $rResultDiasGuardiasRG = 0;

            if (!isset($persona)) {
                $persona =0;
            }
            if ($persona == 0) {
                $personalcargo = $this->getRequest()->getSession()->get('personal');
            } else {

                if ($idtipoliquidacion != 40) {
                    if ($HACupos->getCuentas()->getId() != 24) {

                        // Si quiere pasar el agente por get, verifico que tenga acceso a este cupo
                        if ($this->permiteCargaAgente($persona,$modocarga,$idtipoliquidacion, $HACupos->getCuentas()->getId(),$HACupos->getCupos()->getAnio(),$HACupos->getCupos()->getMes()) == false) {
                            $direccion = "liquidaciones_cuposliquidacion";
                            $this->getRequest()->getSession()->getFlashBag()->add(
                                'aviso_error',
                                'El agente no puede ser cargado en este cupo. '
                            );
                            return $this->redirect($this->generateUrl($direccion));
                        }
                        ////////////////////////// FIN  ////////////////////////////////////////////////////
                    }
                }
            }

            if ($TipoBusqueda == 'agente') {
                $session->set('personal', $persona);
                $personalcargo = $this->getRequest()->getSession()->get('personal');
                //Vuelvo a buscar a la persona para trearme losd atos de esta/////////////////
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$personalcargo.',4';
                /*$stmt = $this   ->getDoctrine()
                                ->getManager('odbc_haberes')
                                ->getConnection()
                                ->prepare($sql);
                $rResult = $stmt->fetch();*/

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmt = $conn->prepare($sql);

                $stmt->execute();
                //$rResult = 0;
                $rResult = $stmt->fetchAll();

            } else {
                 if ($persona != 0) {
                    $session->set('refpersonalReemplazo', $persona);
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$this->getRequest()->getSession()->get('refpersonalReemplazo').',4';
                    /*$stmt = $this   ->getDoctrine()
                                    ->getManager('odbc_haberes')
                                    ->getConnection()
                                    ->prepare($sql);
                    $rResultReemplazo = $stmt->fetch();
                    */
                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResultReemplazo = 0;
                    $rResultReemplazo = $stmt->fetchAll();


                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$this->getRequest()->getSession()->get('personal').',4';
                    /*$stmt = $this   ->getDoctrine()
                                    ->getManager('odbc_haberes')
                                    ->getConnection()
                                    ->prepare($sql);
                    $rResult = $stmt->fetch();*/
                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();

                    $personalcargo = $this->getRequest()->getSession()->get('personal');




                 } else {
                    $session->set('personal', $personalcargo);
                    $personalcargo = $this->getRequest()->getSession()->get('personal');
                    //Vuelvo a buscar a la persona para trearme losd atos de esta/////////////////
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$personalcargo.',4';
                    /*$stmt = $this   ->getDoctrine()
                                    ->getManager('odbc_haberes')
                                    ->getConnection()
                                    ->prepare($sql);
                    $rResult = $stmt->fetch();*/

                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();
                 }
            }



            /////////////////////// PARA LOS ACUMULADORES /////////////////
            $Fecha = $em->createQuery('SELECT c.anio, c.mes, c.monto FROM LiquidacionesCuposAnualesBundle:Cupos c
                      WHERE c.id = :idCupo
                      GROUP BY c.anio, c.mes, c.monto')->setParameter('idCupo', $HACupos->getCupos()->getId())->getResult();
            $anio = $Fecha[0]["anio"];
            $mes = $Fecha[0]["mes"];


            $total = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $HACupos->getCupos()->getId())->getResult();

            if ($total != null) {
                $totalGastadoCupoMensual = $total[0]["total"] == 0 ? 0 : $total[0]["total"];
            } else {
                $totalGastadoCupoMensual = 0;
            }




            //TRAIGO EL VALOR DE LA GUARDIA /////////////
            $em = $this->getDoctrine()->getManager("ms_haberes_web");
            $query = $em->createQuery(
                "SELECT cc.monto
                   FROM LiquidacionesHaberesBundle:HAConceptosValor cc
                  WHERE cc.refConcepto in (514,515,516,517) and '".date("Y-m-d", strtotime($fechanovedad))."' >= cc.vigDesde and '".date("Y-m-d", strtotime($fechanovedad))."' <= cc.vigHasta
                  order by cc.refConcepto"
            );

            $conceptosValor = $query->getResult();

            $concepto24hs = $conceptosValor[0]['monto'];
            $concepto12hs = $conceptosValor[1]['monto'];
            $concepto24hsferiado = $conceptosValor[2]['monto'];
            $concepto12hsferiado = $conceptosValor[3]['monto'];
            /////////////////////////////////////////////


            //TRAIGO LAS GUARDIAS QUE HIZO EN LA FECHA DE LA NOVEDAD /////////////
            $em = $this->getDoctrine()->getManager("ms_haberes_web");
            $sql = "EXEC haberes.Liquidacion.spTraerSaldoHorasReemplazosguardia ".$persona.",".substr($fechanovedad,3,2).",".substr($fechanovedad,-4).",'".date("Y-m-d", strtotime($fechanovedad))."'";
            /*$stmt = $this   ->getDoctrine()
                            ->getManager('odbc_haberes')
                            ->getConnection()
                            ->prepare($sql);
            $rResult = $stmt->fetch();*/

            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $rResultSaldosRG = 0;
            $rResultSaldosRG = $stmt->fetchAll();

            //die(var_dump($rResultSaldosRG));

            $gastado24hs = $rResultSaldosRG[0]['Conc601'];
            $gastado12hs = $rResultSaldosRG[0]['Conc602'];
            $gastado24hsferiado = $rResultSaldosRG[0]['Conc603'];
            $gastado12hsferiado = $rResultSaldosRG[0]['Conc604'];


            /////////////////////////////////////////////
            if ($idtipoliquidacion == 40) {
                $totalRGsql = "SELECT
                        SUM((case WHEN l.idConcepto = 365 THEN l.rGCantHsGuardia ELSE 0 END)) as docehs,
                        SUM((case WHEN l.idConcepto = 364 THEN l.rGCantHsGuardia ELSE 0 END)) as veinticuatrohs,
                        SUM((case WHEN l.idConcepto = 367 THEN l.rGCantHsGuardia ELSE 0 END)) as docehsferiado,
                        SUM((case WHEN l.idConcepto = 366 THEN l.rGCantHsGuardia ELSE 0 END)) as veinticutrohsferiado
                    FROM LiquidacionesWeb.dbo.Liquidaciones l
                  INNER JOIN LiquidacionesWeb.dbo.cuposhatiposliquidacion ca ON l.refcupotipoliquidacion = ca.id
                  INNER JOIN LiquidacionesWeb.dbo.cupos c ON c.id = ca.refcupo
                  WHERE (month(l.rGFecha) = ".substr($fechanovedad,2,2)." and year(l.rGFecha) = ".substr($fechanovedad,-4)."  AND l.idPersonalCargo = ".$personalcargo.") and ca.refCuenta in (25)";
            } else {


                $totalRGsql = "SELECT
                        SUM((case WHEN l.idConcepto = 515 THEN l.rGCantHsGuardia ELSE 0 END)) as docehs,
                        SUM((case WHEN l.idConcepto = 514 THEN l.rGCantHsGuardia ELSE 0 END)) as veinticuatrohs,
                        SUM((case WHEN l.idConcepto = 517 THEN l.rGCantHsGuardia ELSE 0 END)) as docehsferiado,
                        SUM((case WHEN l.idConcepto = 516 THEN l.rGCantHsGuardia ELSE 0 END)) as veinticutrohsferiado
                    FROM LiquidacionesWeb.dbo.Liquidaciones l
                  INNER JOIN LiquidacionesWeb.dbo.cuposhatiposliquidacion ca ON l.refcupotipoliquidacion = ca.id
                  INNER JOIN LiquidacionesWeb.dbo.cupos c ON c.id = ca.refcupo
                  WHERE (month(l.rGFecha) = ".substr($fechanovedad,2,2)." and year(l.rGFecha) = ".substr($fechanovedad,-4)."  AND l.idPersonalCargo = ".$personalcargo.") and c.RefCupoEstado in (1,2) and ca.refCuenta in (24)";

            }
            $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

            $totalRG = $connection ->  prepare($totalRGsql);

            $totalRG->execute();
            //$totalRG->fetchAll();


            if ($totalRG != null) {
                foreach ($totalRG as $a) {
                    $totalRG12 = $a["docehs"] == null ? 0 : $a["docehs"];
                    $totalRG24 = $a["veinticuatrohs"] == null ? 0 : $a["veinticuatrohs"];
                    $totalRG12F = $a["docehsferiado"] == null ? 0 : $a["docehsferiado"];
                    $totalRG24F = $a["veinticutrohsferiado"] == null ? 0 : $a["veinticutrohsferiado"];
                }


            } else {
                $totalRG12 = 0;
                $totalRG24 = 0;
                $totalRG12F = 0;
                $totalRG24F = 0;
            }

            //die(print_r($totalRGsql));

            $saldo = $rResultSaldosRG[0]['saldo'] - ($totalRG12 * 12) - ($totalRG24 * 24) - ($totalRG12F * 12) - ($totalRG24F * 24);

            //////////////////////  FIN ///////////////////////////////////

            $entity = new Liquidaciones();
            if ($idtipoliquidacion == 40) {
                $tipoguardia = array(365=>'12',364=>'24',367=>'12 Hs Feriado',366=>'24 Hs Feriado');
            } else {
                $tipoguardia = array(515=>'12',514=>'24',517=>'12 Hs Feriado',516=>'24 Hs Feriado');
            }
            $form   = $this->createForm(new \Liquidaciones\CuposAnualesBundle\Form\LiquidacionesType($tipoguardia), $entity);

            //////          Traigo los dias de Guardi del Agente  /////////////////
            $sqlDiasGuardias = 'EXEC haberes.haberes.spDiasGuardiaWeb '.$this->getRequest()->getSession()->get('personal');
            /*$stmt = $this   ->getDoctrine()
                            ->getManager('odbc_haberes')
                            ->getConnection()
                            ->prepare($sql);
            $rResultReemplazo = $stmt->fetch();
            */
            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmtDiasGuardias = $conn->prepare($sqlDiasGuardias);

            $stmtDiasGuardias->execute();
            $rResultDiasGuardias = 0;
            $rResultDiasGuardias = $stmtDiasGuardias->fetchAll();
            /////                  FIN                            /////////////////


            //////          Traigo los dias de Guardi del Agente  /////////////////
            if (($this->getRequest()->getSession()->get('refpersonalReemplazo')!=0)) {
                $sqlDiasGuardiasRG = 'EXEC haberes.haberes.spDiasGuardiaWeb '.$this->getRequest()->getSession()->get('refpersonalReemplazo');
                /*$stmt = $this   ->getDoctrine()
                                ->getManager('odbc_haberes')
                                ->getConnection()
                                ->prepare($sql);
                $rResultReemplazo = $stmt->fetch();
                */
                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmtDiasGuardiasRG = $conn->prepare($sqlDiasGuardiasRG);

                $stmtDiasGuardiasRG->execute();
                $rResultDiasGuardiasRG = 0;
                $rResultDiasGuardiasRG = $stmtDiasGuardiasRG->fetchAll();
            }
            /////                  FIN                            /////////////////


            return array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'resultados' => $rResult,
                'resultadosReemplazo' => $rResultReemplazo,
                'cupo' => $idcupo,
                'hacupo' => $HAtlCupo,
                'cupototal'=>$HACupos->getCupos()->getMonto(),
                'cupogastado'=>$totalGastadoCupoMensual,
                'rg12' => $totalRG12 + ($gastado12hs/12),
                'rg24' => $totalRG24 + ($gastado24hs/24),
                'rg12f'=> $totalRG12F + ($gastado12hsferiado/12),
                'rg24f'=> $totalRG24F + ($gastado24hsferiado/24),
                'conceptoValor24hs'=>$concepto24hs,
                'conceptoValor12hs'=>$concepto12hs,
                'conceptoValor24hsFeriado'=>$concepto24hsferiado,
                'conceptoValor12hsFeriado'=>$concepto12hsferiado,
                'diasGuardias'=>$rResultDiasGuardias,
                'diasGuardiasRG'=>$rResultDiasGuardiasRG,
                'mes'=>$mes,
                'anio'=>$anio,
                'saldo' => $saldo,
                'novedad' => $novedades,
                'fechanovedad'=> str_replace('-', '/', $fechanovedad),
                'fechanovedadaux'=> $fechanovedad,
            );

        } else {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));

        }
    }



    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/fechanovedaddeuda/{refCupo}", name="liquidaciones_fechanovedaddeuda")
     * @Method("GET|POST")
     * @Template()
     */
    public function fechanovedaddeudaAction($refCupo)
    {

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');


		// si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
        if ($this->cupoCerradoPorId($refCupo) == true) {
            $direccion = "liquidaciones_cuposliquidacion";
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
            );
            return $this->redirect($this->generateUrl($direccion));
        }
        ////////////////////////// FIN  ////////////////////////////////////////////////////

        $mes = date('m');
        $anio = date('Y');

        return array(
            'refcupo'=>$refCupo,
        );


    }


    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/fechanovedad/{refCupo}", name="liquidaciones_fechanovedad")
     * @Method("GET|POST")
     * @Template()
     */
    public function fechanovedadAction($refCupo)
    {

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_9')) ||
            (true === $securityContext->isGranted('ROLE_10')) || (true === $securityContext->isGranted('ROLE_11')) ||
            (true === $securityContext->isGranted('ROLE_12')) || (true === $securityContext->isGranted('ROLE_13')) ||
            (true === $securityContext->isGranted('ROLE_14')) || (true === $securityContext->isGranted('ROLE_16')) ||
            (true === $securityContext->isGranted('ROLE_20')) || (true === $securityContext->isGranted('ROLE_17'))
                ){


            $idcupo = $this->getRequest()->getSession()->get('cupo');
            $TipoBusqueda = $this->getRequest()->getSession()->get('tipobusqueda');
            $HAtlCupo = $this->getRequest()->getSession()->get('hatlcupo');
            $idtipoliquidacion = $this->getRequest()->getSession()->get('idtipoliquidacion');
            $modocarga = $this->getRequest()->getSession()->get('modocarga');
            $reemplazo = $this->getRequest()->getSession()->get('reemplazo');

            $fechanovedad = $this->getRequest()->getSession()->get('fechanovedad');

            $session->set('refpersonalReemplazo', 0);



            // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
            if ($this->cupoCerradoPorId($idcupo) == true) {
                $direccion = "liquidaciones_cuposliquidacion";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
                );
                return $this->redirect($this->generateUrl($direccion));
            }
            ////////////////////////// FIN  ////////////////////////////////////////////////////

            /***************        VERIFICO SI ESTOY DENTRO DE LAS FECHAS DE CIERRE ***********************/
            $habilitaFechaCierre = $this->devolverFechaCierre($idcupo);

            if ($habilitaFechaCierre == 0) {
                $direccion = "liquidaciones_cuposliquidacion";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'Sistema no habilitado!!.'
                );
                return $this->redirect($this->generateUrl($direccion));
            }
            /********************         FIN                 **********************************************/


            $em =  $this->getDoctrine()->getManager("ms_haberes_web");

            $HACupos    = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($idcupo);

            $mes = $HACupos->getCupos()->getMes();
            $anio = $HACupos->getCupos()->getAnio();

            $adicional          = $HACupos->getAdicional();
            $nombre             = $HACupos->getCuentas()->getCuenta();

            return array(
                'refcupo'=>$refCupo,
                'mes'=>substr('00'.$mes,-2),
                'anio'=>$anio,
                'diaHasta'=>cal_days_in_month(CAL_GREGORIAN, $mes, $anio),
                'guia'=> $nombre." ".$anio."-".$mes." - Adic: ".$adicional,
            );

        } else {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));

        }


    }



    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/newhs/{persona}", name="liquidaciones_newhs")
     * @Method("GET")
     * @Template()
     */
    public function newhsAction($persona)
    {
        $em =  $this->getDoctrine()->getManager("ms_haberes_web");

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_9')) ||
            (true === $securityContext->isGranted('ROLE_10')) || (true === $securityContext->isGranted('ROLE_11')) ||
            (true === $securityContext->isGranted('ROLE_12')) || (true === $securityContext->isGranted('ROLE_13')) ||
            (true === $securityContext->isGranted('ROLE_14')) || (true === $securityContext->isGranted('ROLE_16')) ||
            (true === $securityContext->isGranted('ROLE_20')) || (true === $securityContext->isGranted('ROLE_17'))
                ){


            $idcupo = $this->getRequest()->getSession()->get('cupo');
            $TipoBusqueda = $this->getRequest()->getSession()->get('tipobusqueda');
            $modocarga = $this->getRequest()->getSession()->get('modocarga');
            $HAtlCupo = $this->getRequest()->getSession()->get('hatlcupo');
            $idtipoliquidacion = $this->getRequest()->getSession()->get('idtipoliquidacion');

            $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($idcupo);


            $cuenta             = $entities->getCuentas()->getModoCarga();
            $HAcupo             = $entities->getId();
            $nombre             = $entities->getCuentas()->getCuenta();
            $anio               = $entities->getCupos()->getAnio();
            $mes                = $entities->getCupos()->getMes();
            $adicional          = $entities->getAdicional();
            $idtipoliquidacion  = $entities->getCuentas()->getIdTipoLiquidacion();


            $existeLiquidacion  = $em->getRepository('LiquidacionesCuposAnualesBundle:Liquidaciones')->findOneBy(array("refCupoTipoLiquidacion"=>$idcupo, "idPersonalCargo"=> $persona));

            if ($existeLiquidacion != null) {
                return $this->redirect($this->generateUrl("liquidaciones_ediths",array("id"=>$existeLiquidacion->getId())));
            }

            if ($entities->getCupos()->getCupoestado()->getId() != 4) {
                // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
                if ($this->cupoCerradoPorId($idcupo) == true) {
                    $direccion = "liquidaciones_cuposliquidacion";
                    $this->getRequest()->getSession()->getFlashBag()->add(
                        'aviso_error',
                        'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
                    );
                    return $this->redirect($this->generateUrl($direccion));
                }
            }
            ////////////////////////// FIN  ////////////////////////////////////////////////////

            /***************        VERIFICO SI ESTOY DENTRO DE LAS FECHAS DE CIERRE ***********************/
            $habilitaFechaCierre = $this->devolverFechaCierre($idcupo);

            if ($habilitaFechaCierre == 0) {
                $direccion = "liquidaciones_cuposliquidacion";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'Sistema no habilitado!!.'
                );
                return $this->redirect($this->generateUrl($direccion));
            }
            /********************         FIN                 **********************************************/

            // Si quiere pasar el agente por get, verifico que tenga acceso a este cupo
            if ($this->permiteCargaAgente($persona,$modocarga,$idtipoliquidacion, $entities->getCuentas()->getId(), $entities->getCupos()->getAnio(),$entities->getCupos()->getMes()) == false) {
                $direccion = "liquidaciones_nomina";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El agente no puede ser cargado en este cupo. '
                );
                return $this->redirect($this->generateUrl('liquidaciones_nomina',array('refCupo' => $idcupo)));
            }
            ////////////////////////// FIN  ////////////////////////////////////////////////////

            /////////////////////// PARA LOS ACUMULADORES /////////////////
            $Fecha = $em->createQuery('SELECT c.anio, c.mes FROM LiquidacionesCuposAnualesBundle:Cupos c
                      WHERE c.id = :idCupo
                      GROUP BY c.anio, c.mes')->setParameter('idCupo', $entities->getCupos()->getId())->getResult();
            $anio = $Fecha[0]["anio"];
            $mes = $Fecha[0]["mes"];

            $total = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $entities->getCupos()->getId())->getResult();

            if ($total != null) {
                $totalGastadoCupoMensual = $total[0]["total"] == 0 ? 0 : $total[0]["total"];
            } else {
                $totalGastadoCupoMensual = 0;
            }

            $totalHS = $em->createQuery('SELECT sum(l.hsExCantSimples) as hssimples,sum(l.hsExCantDobles) as hsdobles FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      JOIN ca.cupos c
                      WHERE (ca.refCuenta not in (1,2,3,4,3,18,18,19) and c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('Anio' => $anio, 'Mes'=> $mes,'idPersonalC'=> $persona,))->getResult();

            //die(var_dump($totalHS));
            if ($totalHS != null) {
                $totalHsSimples = $totalHS[0]["hssimples"] == null ? 0 : $totalHS[0]["hssimples"];
                $totalHsDobles = $totalHS[0]["hsdobles"] == null ? 0 : $totalHS[0]["hsdobles"];
            } else {
                $totalHsSimples = 0;
                $totalHsDobles = 0;
            }

            ////////////////////////  FIN ACUMULADORES //////////////////////////////

            $entity = new Liquidaciones();

            $form   = $this->createForm(new \Liquidaciones\CuposAnualesBundle\Form\LiquidacionesTypeHS(), $entity);



            //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
            $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$persona.',4';
            /*$stmt = $this   ->getDoctrine()
                            ->getManager('odbc_haberes')
                            ->getConnection()
                            ->prepare($sql);

            $rResult = 0;*/

            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $rResult = 0;

            $session->set('refpersonal', $persona);
            $rResult = $stmt->fetchAll();

            //traigo el valor hora del agente
            $anioValorHora  = 0;
            $mesValorHora  = 0;

            $mesValorHora = $mes;
            $anioValorHora = $anio;

            $sqlVH = "exec haberes.haberes.spTraerValorHoraLiquidaciones ".$mesValorHora.",".$anioValorHora.",".$persona;
            //die(var_dump($sqlVH));
            /*$stmtVH = $this   ->getDoctrine()
                            ->getManager('odbc_haberes')
                            ->getConnection()
                            ->prepare($sqlVH);

            $rResultVH = 0;

            $rResultVH = $stmtVH->fetch();*/

            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmt = $conn->prepare($sqlVH);

            $stmt->execute();
            //$rResultVH = 0;
            $rResultVH = $stmt->fetchAll();
            //die(var_dump($rResultVH));
            //die(var_dump($rResultVH));
            //die(var_dump($rResultVH));


            return array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'resultados' => $rResult,
                'cupo' => $idcupo,
                'hacupo' => $HAtlCupo,
                'cupototal'=>$entities->getCupos()->getMonto(),
                'cupogastado'=>$totalGastadoCupoMensual,
                'valorhora'=>  number_format($rResultVH[0]["valorhora"],2),
                'horassimples'=> $totalHsSimples,
                'horasdobles'=>$totalHsDobles,
                'guia'=> $nombre." ".$anio."-".$mes." - Adic: ".$adicional
            );

        } else {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));

        }
    }


    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/newhsinvestigacion/{persona}", name="liquidaciones_newhsinvestigacion")
     * @Method("GET")
     * @Template()
     */
    public function newhsinvestigacionAction($persona)
    {
        $em =  $this->getDoctrine()->getManager("ms_haberes_web");

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_22'))
                ){


            $idcupo = $this->getRequest()->getSession()->get('cupo');
            $TipoBusqueda = $this->getRequest()->getSession()->get('tipobusqueda');
            $modocarga = $this->getRequest()->getSession()->get('modocarga');
            $HAtlCupo = $this->getRequest()->getSession()->get('hatlcupo');
            $idtipoliquidacion = $this->getRequest()->getSession()->get('idtipoliquidacion');

            $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($idcupo);


            $cuenta             = $entities->getCuentas()->getModoCarga();
            $HAcupo             = $entities->getId();
            $nombre             = $entities->getCuentas()->getCuenta();
            $anio               = $entities->getCupos()->getAnio();
            $mes                = $entities->getCupos()->getMes();
            $adicional          = $entities->getAdicional();
            $idtipoliquidacion  = $entities->getCuentas()->getIdTipoLiquidacion();


            $existeLiquidacion  = $em->getRepository('LiquidacionesCuposAnualesBundle:Liquidaciones')->findOneBy(array("refCupoTipoLiquidacion"=>$idcupo, "idPersonalCargo"=> $persona));

            if ($existeLiquidacion != null) {
                return $this->redirect($this->generateUrl("liquidaciones_edithsinvestigacion",array("id"=>$existeLiquidacion->getId())));
            }
            // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
            if ($this->cupoCerradoPorId($idcupo) == true) {
                $direccion = "liquidaciones_cuposliquidacion";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
                );
                return $this->redirect($this->generateUrl($direccion));
            }
            ////////////////////////// FIN  ////////////////////////////////////////////////////

            /***************        VERIFICO SI ESTOY DENTRO DE LAS FECHAS DE CIERRE ***********************/
            $habilitaFechaCierre = $this->devolverFechaCierre($idcupo);

            if ($habilitaFechaCierre == 0) {
                $direccion = "liquidaciones_cuposliquidacion";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'Se cumplio la fecha de Cierre o no fue cargada!!.'
                );
                return $this->redirect($this->generateUrl($direccion));
            }
            /********************         FIN                 **********************************************/

            // Si quiere pasar el agente por get, verifico que tenga acceso a este cupo
            if ($this->permiteCargaAgente($persona,$modocarga,$idtipoliquidacion, $entities->getCuentas()->getId(), $entities->getCupos()->getAnio(),$entities->getCupos()->getMes()) == false) {
                $direccion = "liquidaciones_nomina";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El agente no puede ser cargado en este cupo. '
                );
                return $this->redirect($this->generateUrl('liquidaciones_nomina',array('refCupo' => $idcupo)));
            }
            ////////////////////////// FIN  ////////////////////////////////////////////////////

            /////////////////////// PARA LOS ACUMULADORES /////////////////
            $Fecha = $em->createQuery('SELECT c.anio, c.mes FROM LiquidacionesCuposAnualesBundle:Cupos c
                      WHERE c.id = :idCupo
                      GROUP BY c.anio, c.mes')->setParameter('idCupo', $entities->getCupos()->getId())->getResult();
            $anio = $Fecha[0]["anio"];
            $mes = $Fecha[0]["mes"];


            $totalHS = $em->createQuery('SELECT sum(l.hsExCantSimples) as hssimples,sum(l.hsExCantDobles) as hsdobles FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      JOIN ca.cupos c
                      WHERE (c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('Anio' => $anio, 'Mes'=> $mes,'idPersonalC'=> $persona,))->getResult();

            //die(var_dump($totalHS));
            if ($totalHS != null) {
                $totalHsSimples = $totalHS[0]["hssimples"] == null ? 0 : $totalHS[0]["hssimples"];
                $totalHsDobles = $totalHS[0]["hsdobles"] == null ? 0 : $totalHS[0]["hsdobles"];
            } else {
                $totalHsSimples = 0;
                $totalHsDobles = 0;
            }

            ////////////////////////  FIN ACUMULADORES //////////////////////////////

            $entity = new Liquidaciones();

            $form   = $this->createForm(new \Liquidaciones\CuposAnualesBundle\Form\LiquidacionesTypeHS(), $entity);



            //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
            $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$persona.',4';
            /*$stmt = $this   ->getDoctrine()
                            ->getManager('odbc_haberes')
                            ->getConnection()
                            ->prepare($sql);

            $rResult = 0;*/

            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $rResult = 0;

            $session->set('refpersonal', $persona);
            $rResult = $stmt->fetchAll();

            //die(var_dump($rResult));

            //traigo el valor hora del agente
            $anioValorHora  = 0;
            $mesValorHora  = 0;

            $mesValorHora = $mes;
            $anioValorHora = $anio;

            // objeto con los posibles valores horas
            $refregimenestatutario = $rResult[0]["IdRegimenEstatutario"];
            $refagrupamiento = $rResult[0]["IdAgrupamiento"];
            $refencasillamiento = $rResult[0]["IdCodEncasillamiento"];

            $sqlValidacionModulos = "select
              tm.id, tm.modulo, v.monto
              from        LiquidacionesWeb.dbo.TipoModulos tm
              inner
              join        LiquidacionesWeb.dbo.ValorModulo v
              on          v.reftipomodulos = tm.id";

            $stmt = $conn->prepare($sqlValidacionModulos);

            $stmt->execute();
            $rResultVM = 0;

            $rResultVM = $stmt->fetchAll();

            if ($rResultVM == null) {
                $sqlValidacionModulos = "select
                0 as id,'No se puede cargar' as modulo, 0 as monto
                ";

                $stmt = $conn->prepare($sqlValidacionModulos);

                $stmt->execute();
                $rResultVM = 0;

                $rResultVM = $stmt->fetchAll();

            }

            //die(var_dump($rResultVM));






            return array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'resultados' => $rResult,
                'cupo' => $idcupo,
                'hacupo' => $HAtlCupo,
                'cupototal'=>$entities->getCupos()->getMonto(),
                'valorhora'=>  $rResultVM,
                'guia'=> $nombre." ".$anio."-".$mes." - Adic: ".$adicional
            );

        } else {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));

        }
    }





    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/newmonto/{persona}", name="liquidaciones_newmonto")
     * @Method("GET")
     * @Template()
     */
    public function newmontoAction($persona)
    {
        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_9')) ||
            (true === $securityContext->isGranted('ROLE_10')) || (true === $securityContext->isGranted('ROLE_11')) ||
            (true === $securityContext->isGranted('ROLE_12')) || (true === $securityContext->isGranted('ROLE_13')) ||
            (true === $securityContext->isGranted('ROLE_14')) || (true === $securityContext->isGranted('ROLE_16')) ||
            (true === $securityContext->isGranted('ROLE_20')) || (true === $securityContext->isGranted('ROLE_17'))
                ){


            $idcupo = $this->getRequest()->getSession()->get('cupo');
            $TipoBusqueda = $this->getRequest()->getSession()->get('tipobusqueda');
            $modocarga = $this->getRequest()->getSession()->get('modocarga');
            $HAtlCupo = $this->getRequest()->getSession()->get('hatlcupo');
            $idtipoliquidacion = $this->getRequest()->getSession()->get('idtipoliquidacion');

            $em =  $this->getDoctrine()->getManager("ms_haberes_web");

            // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
            if ($this->cupoCerradoPorId($idcupo) == true) {
                $direccion = "liquidaciones_cuposliquidacion";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
                );
                return $this->redirect($this->generateUrl($direccion));
            }
            ////////////////////////// FIN  ////////////////////////////////////////////////////


            /***************        VERIFICO SI ESTOY DENTRO DE LAS FECHAS DE CIERRE ***********************/
            $habilitaFechaCierre = $this->devolverFechaCierre($idcupo);

            if ($habilitaFechaCierre == 0) {
                $direccion = "liquidaciones_cuposliquidacion";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'Sistema no habilitado!!.'
                );
                return $this->redirect($this->generateUrl($direccion));
            }
            /********************         FIN                 **********************************************/

            $HACupos    = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($idcupo);

            // Si quiere pasar el agente por get, verifico que tenga acceso a este cupo
            if ($this->permiteCargaAgente($persona,$modocarga,$idtipoliquidacion, $HACupos->getCuentas()->getId(), $HACupos->getCupos()->getAnio(),$HACupos->getCupos()->getMes()) == false) {
                $direccion = "liquidaciones_nomina";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El agente no puede ser cargado en este cupo. '
                );
                return $this->redirect($this->generateUrl('liquidaciones_nomina',array('refCupo' => $idcupo)));
            }
            ////////////////////////// FIN  ////////////////////////////////////////////////////
            //
            //
            /////////////////////// PARA LOS ACUMULADORES /////////////////
            $Fecha = $em->createQuery('SELECT c.anio, c.mes FROM LiquidacionesCuposAnualesBundle:Cupos c
                      WHERE c.id = :idCupo
                      GROUP BY c.anio, c.mes')->setParameter('idCupo', $HACupos->getCupos()->getId())->getResult();
            $anio = $Fecha[0]["anio"];
            $mes = $Fecha[0]["mes"];

            $total = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $HACupos->getCupos()->getId())->getResult();

            if ($total != null) {
                $totalGastadoCupoMensual = $total[0]["total"] == 0 ? 0 : $total[0]["total"];
            } else {
                $totalGastadoCupoMensual = 0;
            }
            /*
            $parametersHS = array(
                'idCupo' => $idcupo,
                'idpc' => $persona
            );*/

            $totalMONTO = $em->createQuery('SELECT sum(l.montoTotalCalculado) as monto FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      JOIN ca.cupos c
                      WHERE (c.anio= :Anio and c.mes = :Mes AND ca.refCupo= :idCupo AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('Anio' => $anio, 'Mes'=> $mes,'idCupo'=> $HACupos->getCupos()->getId(),'idPersonalC'=> $persona,))->getResult();
            //die(var_dump($HAtlCupo));
            if ($totalMONTO != null) {
                $total = $totalMONTO[0]["monto"]== null ? 0 : $totalMONTO[0]["monto"];

            } else {
                $total = 0;
            }





            ////////////////////////  FIN ACUMULADORES //////////////////////////////

            $entity = new Liquidaciones();

            $cuposhatl = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($idcupo);

            $entity->setCuposhatipoliquidacion($cuposhatl);
            $form   = $this->createForm(new \Liquidaciones\CuposAnualesBundle\Form\LiquidacionesTypeM(), $entity);


            //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
            $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$persona.",4,".$usuarioDependencia;
            /*$stmt = $this   ->getDoctrine()
                            ->getManager('odbc_haberes')
                            ->getConnection()
                            ->prepare($sql);

            $rResult = 0;


            $session->set('refpersonal', $persona);
            $rResult = $stmt->fetch();*/


            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $rResult = 0;

            $session->set('personal', $persona);
            $rResult = $stmt->fetchAll();




            return array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'resultados' => $rResult,
                'cupo' => $idcupo,
                'hacupo' => $HAtlCupo,
                'cupototal'=>$HACupos->getCupos()->getMonto(),
                'cupogastado'=>$totalGastadoCupoMensual,
                'montoCargado'=> $total,
            );

        } else {

            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));

        }
    }



    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/seleccionar", name="liquidaciones_seleccionar")
     * @Method("POST")
     * @Template()
     */
    public function seleccionarAction(Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();

        $em =  $this->getDoctrine()->getManager("ms_haberes_web");

        $session = $this->getRequest()->getSession();

        $idcupo = $this->getRequest()->getSession()->get('cupo');
        $modocarga = $this->getRequest()->getSession()->get('modocarga');
        $idtipoliquidacion = $this->getRequest()->getSession()->get('idtipoliquidacion');
        $reemplazo = $this->getRequest()->getSession()->get('reemplazo');
        $TipoBusqueda = $this->getRequest()->getSession()->get('tipobusqueda');

        //die(var_dump($this->getRequest()->getSession()->get('refpersonal')));

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($idcupo);

        $cuenta             = $entities->getCuentas()->getModoCarga();
        $HAcupo             = $entities->getId();
        $nombre             = $entities->getCuentas()->getCuenta();
        $anio               = $entities->getCupos()->getAnio();
        $mes                = $entities->getCupos()->getMes();
        $adicional          = $entities->getAdicional();
        $idtipoliquidacion  = $entities->getCuentas()->getIdTipoLiquidacion();
        $idCuenta           = $entities->getCuentas()->getId();

        $valor = 0;
        switch ($idCuenta) {
            case 22:
                $valor = 2;
                break;
            case 21:
                $valor = 3;
                break;
            case 23:
                $valor = 4;
                break;
            case 24:
                $valor = 2;
                break;
            case 7:
                $valor = 5;
                break;
            case 25:
                $valor = 4;
                break;
            default:
                $valor = 3;
                break;
        }

        if ($TipoBusqueda != 'agente') {
            $valor = 1; //persona a la que voy a reemplazar
        }

        $data = $request->request->all();

        $busqueda = $data['busqueda'];
        $fechaNovedadSql = '';
        if (isset($data['fechanovedad'])) {
            $fechanovedad = $data['fechanovedad'];
            //$fechaNovedadSql = ",".(integer)substr($fechanovedad,0,2).", ".(integer)substr($fechanovedad,3,2).",".substr($fechanovedad,-4);
            $fechaNovedadSql = ",'".date("Y/m/d", strtotime($fechanovedad))."',".date('N', strtotime($fechanovedad));
        } else {
            $fechanovedad = '';
        }

        $filtro = $data['filtro'];

        if (isset($data['persona'])) {
            $persona = $data['persona'];
        } else {
            $persona = 0;
        }


        /*
         * @liquidacion int = null,
	@PeriodoAnio int = null,
	@PeriodoMes int = null,
	@valor int = null
         *
         *
         */
        switch ($filtro) {
            case 1:
                $sql = "EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones ".$busqueda.','.$filtro.', '.$entities->getCupos()->getIdDependencia().','.$idtipoliquidacion.', '.$anio.', '.$mes.','.$valor.$fechaNovedadSql;
                break;
            case 2:
                $busqueda = ltrim(rtrim(str_replace(' ', '_', $busqueda)));
                $busqueda = str_replace('ñ', 'n', $busqueda);

                $sql = "EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '".$busqueda."',".$filtro.', '.$entities->getCupos()->getIdDependencia().','.$idtipoliquidacion.', '.$anio.', '.$mes.','.$valor.$fechaNovedadSql;
                break;
            case 3:
                $sql = "EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones ".$busqueda.','.$filtro.', '.$entities->getCupos()->getIdDependencia().','.$idtipoliquidacion.', '.$anio.', '.$mes.','.$valor.$fechaNovedadSql;
                break;
        }
        //die(var_dump($sql));

        $em =  $this->getDoctrine()->getManager("ms_haberes_web");

        /*$stmt = $this   ->getDoctrine()
                        ->getManager('odbc_haberes')
                        ->getConnection()
                        ->prepare($sql);

        $rResult = $stmt->fetch();*/

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();
        $rResult = 0;

        $rResult = $stmt->fetchAll();

        //die(var_dump($rResult));
        $vista = '';

        if ($idCuenta == 26) {
            $vista = 'newhsinvestigacion';
        } else {
            switch ($modocarga) {
            case 'rg':
                $vista = 'new';
                break;
            case 'horas':
                $vista = 'newhs';
                break;
            default:
                $vista = 'newmonto';
                break;
            }
        }

        //die(var_dump($this->getRequest()->getSession()->get('persona')));
        return array(
            //'entity' => $entity,
            //'form'   => $form->createView(),
            'resultados'  => $rResult,
            'cupo' => $idcupo,
            'modocarga' => $this->getRequest()->getSession()->get('modocarga'),
            'vista'=>$vista,
            'idtipoliquidacion'=>$idtipoliquidacion,
            'reemplazo' => $reemplazo,
            'idcuenta'=>$idCuenta,
            'fechanovedad'=> $fechanovedad,
            'guia'=> $nombre." ".$anio."-".$mes." - Adic: ".$adicional,
            'persona'=>$persona,
        );
    }




    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/seleccionarhistorico/", name="liquidaciones_seleccionarhistorico")
     * @Method("POST")
     * @Template()
     */
    public function seleccionarhistoricoAction(Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $em =  $this->getDoctrine()->getManager("ms_haberes_web");

        //Seguridad
        $securityContext = $this->get('security.context');

        $data = $request->request->all();

        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        $busqueda = $data['busqueda'];

        $filtro = $data['filtro'];

        if ((true === $securityContext->isGranted('ROLE_1')) || (true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_15')) || (true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17')) || (true === $securityContext->isGranted('ROLE_18')) || (true === $securityContext->isGranted('ROLE_22'))) {
            switch ($filtro) {
                case 1:
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$busqueda.','.$filtro;
                    break;
                case 2:
                    $busqueda = ltrim(rtrim(str_replace(' ', '_', $busqueda)));
                    $busqueda = str_replace('ñ', 'n', $busqueda);
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$busqueda.','.$filtro;
                    break;
                case 3:
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$busqueda.','.$filtro;
                    break;
            }

        } else {
            switch ($filtro) {
                case 1:
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$busqueda.','.$filtro.','.$usuarioDependencia;
                    break;
                case 2:
                    $busqueda = ltrim(rtrim(str_replace(' ', '_', $busqueda)));
                    $busqueda = str_replace('ñ', 'n', $busqueda);
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$busqueda.','.$filtro.','.$usuarioDependencia;
                    break;
                case 3:
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$busqueda.','.$filtro.','.$usuarioDependencia;
                    break;
            }
        }


        $em =  $this->getDoctrine()->getManager("ms_haberes_web");

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();
        $rResult = 0;

        $rResult = $stmt->fetchAll();

        return array(
            'resultados'  => $rResult,
        );
    }



    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/seleccionareditar", name="liquidaciones_seleccionareditar")
     * @Method("POST")
     * @Template()
     */
    public function seleccionareditarAction(Request $request)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        $em =  $this->getDoctrine()->getManager("ms_haberes_web");

        $session = $this->getRequest()->getSession();
        $ideditar = $this->getRequest()->getSession()->get('ideditar');

        $liquidacion = new Liquidaciones();
        $liquidacion = $em->getRepository('LiquidacionesCuposAnualesBundle:Liquidaciones')->find($ideditar);

        $idcupo = $liquidacion->getCuposhatipoliquidacion()->getCupos()->getId();
        $modocarga = $liquidacion->getCuposhatipoliquidacion()->getCuentas()->getModoCarga();
        $idtipoliquidacion = $liquidacion->getCuposhatipoliquidacion()->getCuentas()->getIdTipoLiquidacion();
        $TipoBusqueda = $this->getRequest()->getSession()->get('tipobusqueda');

        $anio = $liquidacion->getCuposhatipoliquidacion()->getCupos()->getAnio();
        $mes  = $liquidacion->getCuposhatipoliquidacion()->getCupos()->getMes();

        $idCuenta = $liquidacion->getCuposhatipoliquidacion()->getCuentas()->getId();

        $valor = 0;
        switch ($idCuenta) {
            case 22:
                $valor = 2;
                break;
            case 21:
                $valor = 3;
                break;
            case 23:
                $valor = 4;
                break;
            case 7:
                $valor = 5;
                break;
            case 25:
                $valor = 4;
                break;
            default:
                $valor = 3;
                break;
        }

        if ($TipoBusqueda != 'agente') {
            $valor = 1; //persona a la que voy a reemplazar
        }

        $data = $request->request->all();

        $busqueda = $data['busqueda'];

        $filtro = $data['filtro'];

        $fechaNovedadSql = "";

        //die(var_dump($liquidacion->getRGFecha()->format("Y-m-d")));
        if ($liquidacion->getRGFecha() != null) {
            $fechaNovedadSql = ",'".$liquidacion->getRGFecha()->format("Y-m-d")."',".$liquidacion->getRGFecha()->format("N");
        } else {
            $fechaNovedadSql = "";

        }
        switch ($filtro) {
            case 1:
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$busqueda.','.$filtro.', '.$liquidacion->getCuposhatipoliquidacion()->getCupos()->getIdDependencia().','.$idtipoliquidacion.', '.$anio.', '.$mes.','.$valor.$fechaNovedadSql;
                break;
            case 2:
                $busqueda = ltrim(rtrim(str_replace(' ', '_', $busqueda)));
                $busqueda = str_replace('ñ', 'n', $busqueda);
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$busqueda.','.$filtro.', '.$liquidacion->getCuposhatipoliquidacion()->getCupos()->getIdDependencia().','.$idtipoliquidacion.', '.$anio.', '.$mes.','.$valor.$fechaNovedadSql;
                break;
            case 3:
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$busqueda.','.$filtro.', '.$liquidacion->getCuposhatipoliquidacion()->getCupos()->getIdDependencia().','.$idtipoliquidacion.', '.$anio.', '.$mes.','.$valor.$fechaNovedadSql;
                break;
        }

        $em =  $this->getDoctrine()->getManager("ms_haberes_web");

        /*$stmt = $this   ->getDoctrine()
                        ->getManager('odbc_haberes')
                        ->getConnection()
                        ->prepare($sql);

        $rResult = $stmt->fetch();*/

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();
        $rResult = 0;

        $rResult = $stmt->fetchAll();

        $vista = '';
        switch ($modocarga) {
            case 'rg':
                $vista = 'new';
                break;
            case 'horas':
                $vista = 'newhs';
                break;
            default:
                $vista = 'newmonto';
                break;
        }

        return array(
            //'entity' => $entity,
            //'form'   => $form->createView(),
            'ideditar' => $ideditar,
            'resultados'  => $rResult,
            'cupo' => $idcupo,
            'modocarga' => $modocarga,
            'vista'=>$vista,
            'idtipoliquidacion'=>$idtipoliquidacion,
            'idcuenta'=>$idCuenta,
        );
    }



    /**
     * Finds and displays a Liquidaciones entity.
     *
     * @Route("/show/{id}", name="liquidaciones_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Liquidaciones')->find($id);

        if ($entity == null) {
            $entity = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total,
                                            l.idPersonalCargo FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  WHERE ca.id= :idCupo
                  GROUP BY l.idPersonalCargo')->setParameter('idCupo', $id)->getResult();
            //die(var_dump($entity));
        }

        $entityC = $em->createQuery('SELECT cc.modoCarga, cc.cuenta, cc.id FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  JOIN ca.cuentas cc
                  WHERE l.id= :idLiq ')->setParameter('idLiq', $id)->getResult();

        $modocarga = $entityC[0]['modoCarga'];
        $cuenta = $entityC[0]['cuenta'];
        $idcuenta = $entityC[0]['id'];

        //die(var_dump($modocarga));

        $session = $this->getRequest()->getSession();
        $idcupo = $entity->getRefCupoTipoLiquidacion();
        $cupo = $entity->getCuposhatipoliquidacion()->getId();
        $idtipoliquidacion = $entity->getCuposhatipoliquidacion()->getCuentas()->getIdTipoLiquidacion();
        $CupoEstado = $entity->getCuposhatipoliquidacion()->getCupos()->getCupoestado()->getCupoEstado();

        $persona = $entity->getIdPersonalCargo();
        //die(var_dump($persona));
        //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
        $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$persona.',4';
        /*$stmt = $this   ->getDoctrine()
                        ->getManager('odbc_haberes')
                        ->getConnection()
                        ->prepare($sql);

        $rResult = 0;

        $rResult = $stmt->fetch();*/

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->execute();
        $rResult = 0;

        $rResult = $stmt->fetchAll();

        $rResultReemplazo = 0;


        $tipoguardia = array(515=>'12 Hs',514=>'24 Hs',517=>'12 Hs Feriado',516=>'24 Hs Feriado');
        switch ($modocarga) {
            case 'rg':
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.($entity->getRGIdPersonalCargo() == '' ? 0 : $entity->getRGIdPersonalCargo()).',4';
                /*$stmt = $this   ->getDoctrine()
                                ->getManager('odbc_haberes')
                                ->getConnection()
                                ->prepare($sql);
                $rResultReemplazo = $stmt->fetch();*/

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmt = $conn->prepare($sql);

                $stmt->execute();
                $rResultReemplazo = 0;

                $rResultReemplazo = $stmt->fetchAll();
                //$concepto = '12 Hs';

                if ($idtipoliquidacion != 40) {

                    switch ($entity->getIdConcepto()) {
                        case '515':
                            $concepto = '12 Hs';
                            break;
                        case '514':
                            $concepto = '24 Hs';
                            break;
                        case '517':
                            $concepto = '12 Hs Feriado';
                            break;
                        case '516':
                            $concepto = '24 Hs Feriado';
                            break;
                    }
                } else {

                    switch ($entity->getIdConcepto()) {
                        case '365':
                            $concepto = '12 Hs';
                            break;
                        case '364':
                            $concepto = '24 Hs';
                            break;
                        case '367':
                            $concepto = '12 Hs Feriado';
                            break;
                        case '366':
                            $concepto = '24 Hs Feriado';
                            break;
                    }

                }
                break;
            case 'horas':
                $concepto = '49';
                break;
            default:
                $concepto = $entity->getIdConcepto();
                break;
        }


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Liquidaciones entity.');
        }

        $deleteForm = $this->createDeleteForm($id,$cupo);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'modo' => $modocarga,
            'resultadosReemplazo' => $rResultReemplazo,
            'resultados'  => $rResult,
            'concepto'=>$concepto,
            'cupo'=>$cupo,
            'CupoEstado'  => $CupoEstado,
            'idcuenta'=>$idcuenta,
        );
    }

    /**
     * Displays a form to edit an existing Liquidaciones entity.
     *
     * @Route("/{id}/{refRGpersonalcargo}/edit", name="liquidaciones_edit")
     * @Method("GET|POST")
     * @Template()
     */
    public function editAction($id, $refRGpersonalcargo = null)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entity = new Liquidaciones();
        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Liquidaciones')->find($id);

        //die(var_dump($entity->getRGFecha()));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Liquidaciones entity.');
        }

        $session = $this->getRequest()->getSession();

        $session->set('refpersonalReemplazo', $entity->getRGIdPersonalCargo());

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_9')) ||
            (true === $securityContext->isGranted('ROLE_10')) || (true === $securityContext->isGranted('ROLE_11')) ||
            (true === $securityContext->isGranted('ROLE_12')) || (true === $securityContext->isGranted('ROLE_13')) ||
            (true === $securityContext->isGranted('ROLE_14')) || (true === $securityContext->isGranted('ROLE_16')) ||
            (true === $securityContext->isGranted('ROLE_20')) || (true === $securityContext->isGranted('ROLE_17'))
                ){



            //busco el modo de carga para saber en que formulario entra
            $modo = $entity->getCuposhatipoliquidacion()->getCuentas()->getModoCarga();

            //miro que tipo de liquidacion es para diferenciar ART48 y REEMPLAZO
            $idtipoliquidacion = $entity->getCuposhatipoliquidacion()->getCuentas()->getIdTipoLiquidacion();

            // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
            if ($this->cupoCerradoPorId($entity->getCuposhatipoliquidacion()->getId()) == true) {
                $direccion = "liquidaciones_cuposliquidacion";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
                );
                return $this->redirect($this->generateUrl($direccion));
            }
            ////////////////////////// FIN  ////////////////////////////////////////////////////


            //////////////////  BUSCO LOS AGENTES CARGADOS  //////////////////
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$entity->getIdPersonalCargo().',4';
                /*$stmt = $this   ->getDoctrine()
                                ->getManager('odbc_haberes')
                                ->getConnection()
                                ->prepare($sql);

                $rResult = 0;
                $rResult = $stmt->fetch();*/

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmt = $conn->prepare($sql);

                $stmt->execute();
                $rResult = 0;

                $rResult = $stmt->fetchAll();
            ///////////////////////////////////////// FIN //////////////////////////

            ////////// Reconosco Fechas   /////////////////////////////////////
            $anio = $entity->getCuposhatipoliquidacion()->getCupos()->getAnio();
            $mes = $entity->getCuposhatipoliquidacion()->getCupos()->getMes();
            ////////////////////// FIN   /////////////////////////////

            $session->set('editar',true);
            $session->set('ideditar',$id);
            $session->set('refRGpersonalcargo',$refRGpersonalcargo);

            //////////////////  BUSCO LOS AGENTES CARGADOS  //////////////////
            if ($entity->getRGIdPersonalCargo() != null) {
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.($entity->getRGIdPersonalCargo() == '' ? 0 : $entity->getRGIdPersonalCargo()).',4';
                /*$stmt = $this   ->getDoctrine()
                                ->getManager('odbc_haberes')
                                ->getConnection()
                                ->prepare($sql);

                $rResultReemp = $stmt->fetch();*/

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmt = $conn->prepare($sql);

                $stmt->execute();
                $rResultReemp = 0;

                $rResultReemp = $stmt->fetchAll();
            }

            if ($refRGpersonalcargo != null) {
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$refRGpersonalcargo.',4';
                /*$stmt = $this   ->getDoctrine()
                                ->getManager('odbc_haberes')
                                ->getConnection()
                                ->prepare($sql);
                $rResultReemp = $stmt->fetch();*/

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                $stmt = $conn->prepare($sql);
                $stmt->execute();

                $rResultReemp = $stmt->fetchAll();
            } else {
                $rResultReemp = 0;
            }
            //////////////////  FIN  //////////////////
            /////////////////////// PARA LOS ACUMULADORES /////////////////

            //Traigo el TOTAL cargado para ese cupo
            $total = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $entity->getCuposhatipoliquidacion()->getCupos()->getId())->getResult();

            if ($total != null) {
                $totalGastadoCupoMensual = $total[0]["total"] == 0 ? 0 : $total[0]["total"];
            } else {
                $totalGastadoCupoMensual = 0;
            }

            //////////////////////  FIN ///////////////////////////////////

            if ($idtipoliquidacion == 40) {


                //TRAIGO EL VALOR DE LA GUARDIA /////////////
                $fechanovedad = $entity->getCuposhatipoliquidacion()->getCupos()->getMes().'/'.$entity->getCuposhatipoliquidacion()->getCupos()->getAnio();
		            $em = $this->getDoctrine()->getManager("ms_haberes_web");
		            $query = $em->createQuery(
		                "SELECT cc.monto
		                   FROM LiquidacionesHaberesBundle:HAConceptosValor cc
		                  WHERE cc.refConcepto in (514,515,516,517) and '".date("Y-d-m", strtotime("01/".$fechanovedad))."' >= cc.vigDesde and '".date("Y-d-m", strtotime("01/".$fechanovedad))."' <= cc.vigHasta
		                  order by cc.refConcepto"
		            );

		            $conceptosValor = $query->getResult();

		            $concepto24hs = $conceptosValor[0]['monto'];
		            $concepto12hs = $conceptosValor[1]['monto'];
		            $concepto24hsferiado = $conceptosValor[2]['monto'];
		            $concepto12hsferiado = $conceptosValor[3]['monto'];
                /////////////////////////////////////////////

                switch ($entity->getIdConcepto()) {
                    case '515':
                        $entity->setIdConcepto(365);
                        break;
                    case '514':
                        $entity->setIdConcepto(364);
                        break;
                    case '517':
                        $entity->setIdConcepto(367);
                        break;
                    case '516':
                        $entity->setIdConcepto(366);
                        break;
                }
                $totalRG = $em->createQuery('SELECT
                                sum((case when l.idConcepto = 365 then l.rGCantHsGuardia else 0 end)) as docehs,
                                sum((case when l.idConcepto = 364 then l.rGCantHsGuardia else 0 end)) as veinticuatrohs,
                                sum((case when l.idConcepto = 367 then l.rGCantHsGuardia else 0 end)) as docehsferiado,
                                sum((case when l.idConcepto = 366 then l.rGCantHsGuardia else 0 end)) as veinticutrohsferiado
                            FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                          JOIN l.cuposhatipoliquidacion ca
                          JOIN ca.cupos c
                          WHERE (c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('Anio' => $anio, 'Mes'=> $mes,'idPersonalC'=> $entity->getIdPersonalCargo(),))->getResult();

            } else {

                //TRAIGO EL VALOR DE LA GUARDIA /////////////
                $fechanovedad = $entity->getCuposhatipoliquidacion()->getCupos()->getMes().'/'.$entity->getCuposhatipoliquidacion()->getCupos()->getAnio();
		            $em = $this->getDoctrine()->getManager("ms_haberes_web");
		            $query = $em->createQuery(
		                "SELECT cc.monto
		                   FROM LiquidacionesHaberesBundle:HAConceptosValor cc
		                  WHERE cc.refConcepto in (364,365,366,367) and '".date("Y-d-m", strtotime("01/".$fechanovedad))."' >= cc.vigDesde and '".date("Y-d-m", strtotime("01/".$fechanovedad))."' <= cc.vigHasta
		                  order by cc.refConcepto"
		            );

		            $conceptosValor = $query->getResult();

		            $concepto24hs = $conceptosValor[0]['monto'];
		            $concepto12hs = $conceptosValor[1]['monto'];
		            $concepto24hsferiado = $conceptosValor[2]['monto'];
		            $concepto12hsferiado = $conceptosValor[3]['monto'];
                /////////////////////////////////////////////

                $totalRG = $em->createQuery('SELECT
                                sum((case when l.idConcepto = 515 then l.rGCantHsGuardia else 0 end)) as docehs,
                                sum((case when l.idConcepto = 514 then l.rGCantHsGuardia else 0 end)) as veinticuatrohs,
                                sum((case when l.idConcepto = 517 then l.rGCantHsGuardia else 0 end)) as docehsferiado,
                                sum((case when l.idConcepto = 516 then l.rGCantHsGuardia else 0 end)) as veinticutrohsferiado
                            FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                          JOIN l.cuposhatipoliquidacion ca
                          JOIN ca.cupos c
                          WHERE (c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('Anio' => $anio, 'Mes'=> $mes,'idPersonalC'=>$entity->getIdPersonalCargo(),))->getResult();

            }


            if ($totalRG != null) {
                $totalRG12 = $totalRG[0]["docehs"] == null ? 0 : $totalRG[0]["docehs"];
                $totalRG24 = $totalRG[0]["veinticuatrohs"] == null ? 0 : $totalRG[0]["veinticuatrohs"];
                $totalRG12F = $totalRG[0]["docehsferiado"] == null ? 0 : $totalRG[0]["docehsferiado"];
                $totalRG24F = $totalRG[0]["veinticutrohsferiado"] == null ? 0 : $totalRG[0]["veinticutrohsferiado"];

            } else {
                $totalRG12 = 0;
                $totalRG24 = 0;
                $totalRG12F = 0;
                $totalRG24F = 0;
            }


            //////          Traigo los dias de Guardi del Agente  /////////////////
            $sqlDiasGuardias = 'EXEC haberes.haberes.spDiasGuardiaWeb '.$entity->getIdPersonalCargo();
            /*$stmt = $this   ->getDoctrine()
                            ->getManager('odbc_haberes')
                            ->getConnection()
                            ->prepare($sql);
            $rResultReemplazo = $stmt->fetch();
            */
            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmtDiasGuardias = $conn->prepare($sqlDiasGuardias);

            $stmtDiasGuardias->execute();
            $rResultDiasGuardias = 0;
            $rResultDiasGuardias = $stmtDiasGuardias->fetchAll();


            $rResultDiasGuardiasRG = 0;
            if ($entity->getRGIdPersonalCargo() !=0) {

                $sqlDiasGuardiasRG = 'EXEC haberes.haberes.spDiasGuardiaWeb '.$this->getRequest()->getSession()->get('refpersonalReemplazo');
                /*$stmt = $this   ->getDoctrine()
                                ->getManager('odbc_haberes')
                                ->getConnection()
                                ->prepare($sql);
                $rResultReemplazo = $stmt->fetch();
                */
                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmtDiasGuardiasRG = $conn->prepare($sqlDiasGuardiasRG);

                $stmtDiasGuardiasRG->execute();
                $rResultDiasGuardiasRG = 0;
                $rResultDiasGuardiasRG = $stmtDiasGuardiasRG->fetchAll();
            }
            /////                  FIN                            /////////////////

            $entity->setRGIdPersonalCargo($this->getRequest()->getSession()->get('refpersonalReemplazo'));

            $editForm = $this->createEditForm($entity,$modo);
            $deleteForm = $this->createDeleteForm($id,$entity->getCuposhatipoliquidacion()->getId());




            return array(
                'entity' => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'resultados' => $rResult,
                'resultadosReemplazo' => $rResultReemp,
                'cupo' => $entity->getCuposhatipoliquidacion()->getId(),
                'cupototal'=>$entity->getCuposhatipoliquidacion()->getCupos()->getMonto(),
                'cupogastado'=>$totalGastadoCupoMensual,
                'rg12' => $totalRG12,
                'rg24' => $totalRG24,
                'rg12f'=> $totalRG12F,
                'rg24f'=> $totalRG24F,
                'conceptoValor24hs'=>$concepto24hs,
                'conceptoValor12hs'=>$concepto12hs,
                'conceptoValor24hsFeriado'=>$concepto24hsferiado,
                'conceptoValor12hsFeriado'=>$concepto12hsferiado,
                'diasGuardias'=>$rResultDiasGuardias,
                'diasGuardiasRG' =>$rResultDiasGuardiasRG,
                'idHACTL'=>$entity->getCuposhatipoliquidacion()->getId(),
                'fechanovedad'=>$entity->getRGFecha(),
                'idPersonalCargoRG'=>$this->getRequest()->getSession()->get('refpersonalReemplazo'),
                'anio'=>$entity->getCuposhatipoliquidacion()->getCupos()->getCuposanuales()->getAnio(),
                'mes'=>$entity->getCuposhatipoliquidacion()->getCupos()->getMes(),
            );

        } else {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));

        }

    }




    /**
     * Displays a form to edit an existing Liquidaciones entity.
     *
     * @Route("/{id}/{refRGpersonalcargo}/editdeuda", name="liquidaciones_editdeuda")
     * @Method("GET|POST")
     * @Template()
     */
    public function editdeudaAction($id, $refRGpersonalcargo = null)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entity = new Liquidaciones();
        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Liquidaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Liquidaciones entity.');
        }

        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        $horasDevolver = 0;

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_9')) ||
            (true === $securityContext->isGranted('ROLE_10')) || (true === $securityContext->isGranted('ROLE_11')) ||
            (true === $securityContext->isGranted('ROLE_12')) || (true === $securityContext->isGranted('ROLE_13')) ||
            (true === $securityContext->isGranted('ROLE_14')) || (true === $securityContext->isGranted('ROLE_16')) ||
            (true === $securityContext->isGranted('ROLE_20')) || (true === $securityContext->isGranted('ROLE_17'))
                ){



            //busco el modo de carga para saber en que formulario entra
            $modo = $entity->getCuposhatipoliquidacion()->getCuentas()->getModoCarga();

            //miro que tipo de liquidacion es para diferenciar ART48 y REEMPLAZO
            $idtipoliquidacion = $entity->getCuposhatipoliquidacion()->getCuentas()->getIdTipoLiquidacion();

            // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
            if ($this->cupoCerradoPorId($entity->getCuposhatipoliquidacion()->getId()) == true) {
                $direccion = "liquidaciones_cuposliquidacion";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
                );
                return $this->redirect($this->generateUrl($direccion));
            }
            ////////////////////////// FIN  ////////////////////////////////////////////////////


            //////////////////  BUSCO LOS AGENTES CARGADOS  //////////////////
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$entity->getIdPersonalCargo().',4';
                /*$stmt = $this   ->getDoctrine()
                                ->getManager('odbc_haberes')
                                ->getConnection()
                                ->prepare($sql);

                $rResult = 0;
                $rResult = $stmt->fetch();*/

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmt = $conn->prepare($sql);

                $stmt->execute();
                $rResult = 0;

                $rResult = $stmt->fetchAll();
            ///////////////////////////////////////// FIN //////////////////////////

            ////////// Reconosco Fechas   /////////////////////////////////////
            $anio = $entity->getCuposhatipoliquidacion()->getCupos()->getAnio();
            $mes = $entity->getCuposhatipoliquidacion()->getCupos()->getMes();
            ////////////////////// FIN   /////////////////////////////

            $session->set('editar',true);
            $session->set('ideditar',$id);
            $session->set('refRGpersonalcargo',$refRGpersonalcargo);

            //////////////////  BUSCO LOS AGENTES CARGADOS  //////////////////
            if ($entity->getRGIdPersonalCargo() != null) {
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.($entity->getRGIdPersonalCargo() == '' ? 0 : $entity->getRGIdPersonalCargo()).',4';
                /*$stmt = $this   ->getDoctrine()
                                ->getManager('odbc_haberes')
                                ->getConnection()
                                ->prepare($sql);

                $rResultReemp = $stmt->fetch();*/

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmt = $conn->prepare($sql);

                $stmt->execute();
                $rResultReemp = 0;

                $rResultReemp = $stmt->fetchAll();
            }

            if ($refRGpersonalcargo != null) {
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$refRGpersonalcargo.',4';
                /*$stmt = $this   ->getDoctrine()
                                ->getManager('odbc_haberes')
                                ->getConnection()
                                ->prepare($sql);
                $rResultReemp = $stmt->fetch();*/

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                $stmt = $conn->prepare($sql);
                $stmt->execute();

                $rResultReemp = $stmt->fetchAll();
            } else {
                $rResultReemp = 0;
            }
            //////////////////  FIN  //////////////////
            /////////////////////// PARA LOS ACUMULADORES /////////////////

            //Traigo el TOTAL cargado para ese cupo
            $total = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $entity->getCuposhatipoliquidacion()->getCupos()->getId())->getResult();

            if ($total != null) {
                $totalGastadoCupoMensual = $total[0]["total"] == 0 ? 0 : $total[0]["total"];
            } else {
                $totalGastadoCupoMensual = 0;
            }

            //////////////////////  FIN ///////////////////////////////////

            $fromDate=$entity->getRGFecha();


            //TRAIGO EL VALOR DE LA GUARDIA /////////////
            $em = $this->getDoctrine()->getManager("ms_haberes_web");
            $query = $em->createQuery(
                "SELECT cc.monto
                   FROM LiquidacionesHaberesBundle:HAConceptosValor cc
                  WHERE cc.refConcepto in (514,515,516,517) and '".$fromDate->format("Y-d-m")."' >= cc.vigDesde and '".$fromDate->format("Y-d-m")."' <= cc.vigHasta
                  order by cc.refConcepto"
            );

            $conceptosValor = $query->getResult();

            $concepto24hs = $conceptosValor[0]['monto'];
            $concepto12hs = $conceptosValor[1]['monto'];
            $concepto24hsferiado = $conceptosValor[2]['monto'];
            $concepto12hsferiado = $conceptosValor[3]['monto'];
            /////////////////////////////////////////////

            switch ($entity->getIdConcepto()) {
                case '515':
                    $entity->setIdConcepto(365);
                    $horasDevolver = $entity->getRGCantHsGuardia() * 12;
                    break;
                case '514':
                    $entity->setIdConcepto(364);
                    $horasDevolver = $entity->getRGCantHsGuardia() * 24;
                    break;
                case '517':
                    $entity->setIdConcepto(367);
                    $horasDevolver = $entity->getRGCantHsGuardia() * 12;
                    break;
                case '516':
                    $entity->setIdConcepto(366);
                    $horasDevolver = $entity->getRGCantHsGuardia() * 24;
                    break;
            }



            //TRAIGO LAS GUARDIAS QUE HIZO EN LA FECHA DE LA NOVEDAD /////////////
            $emHaberes = $this->getDoctrine()->getManager("ms_haberes");
            $sql = "EXEC haberes.Liquidacion.spTraerSaldoHorasReemplazosguardia ".$entity->getIdPersonalCargo().",".substr($fromDate->format("m-d-Y"),0,2).",".substr($fromDate->format("m-d-Y"),-4).",'".$fromDate->format("Y-d-m")."'";

            /*$stmt = $this   ->getDoctrine()
                            ->getManager('odbc_haberes')
                            ->getConnection()
                            ->prepare($sql);
            $rResult = $stmt->fetch();*/

            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $rResultSaldosRG = 0;
            $rResultSaldosRG = $stmt->fetchAll();

            $gastado24hs = $rResultSaldosRG[0]['Conc601'];
            $gastado12hs = $rResultSaldosRG[0]['Conc602'];
            $gastado24hsferiado = $rResultSaldosRG[0]['Conc603'];
            $gastado12hsferiado = $rResultSaldosRG[0]['Conc604'];


            /////////////////////////////////////////////

            //die(var_dump($entity->getRGFecha()));



            $totalRGsql = "SELECT
                        SUM((case WHEN l.idConcepto = 515 THEN l.rGCantHsGuardia ELSE 0 END)) as docehs,
                        SUM((case WHEN l.idConcepto = 514 THEN l.rGCantHsGuardia ELSE 0 END)) as veinticuatrohs,
                        SUM((case WHEN l.idConcepto = 517 THEN l.rGCantHsGuardia ELSE 0 END)) as docehsferiado,
                        SUM((case WHEN l.idConcepto = 516 THEN l.rGCantHsGuardia ELSE 0 END)) as veinticutrohsferiado
                    FROM Liquidaciones.Liquidaciones l
                  INNER JOIN Liquidaciones.cuposhatiposliquidacion ca ON l.refcupotipoliquidacion = ca.id
                  INNER JOIN Liquidaciones.cupos c ON c.id = ca.refcupo
                  WHERE (l.rGFecha = convert(date,'".$fromDate->format("Y-d-m")."') AND l.idPersonalCargo = ".$entity->getIdPersonalCargo().")";

            $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

            $totalRG = $connection ->  prepare($totalRGsql);

            $totalRG->execute();
            //$totalRG->fetchAll();


            if ($totalRG != null) {
                foreach ($totalRG as $a) {
                    $totalRG12 = $a["docehs"] == null ? 0 : $a["docehs"];
                    $totalRG24 = $a["veinticuatrohs"] == null ? 0 : $a["veinticuatrohs"];
                    $totalRG12F = $a["docehsferiado"] == null ? 0 : $a["docehsferiado"];
                    $totalRG24F = $a["veinticutrohsferiado"] == null ? 0 : $a["veinticutrohsferiado"];
                }


            } else {
                $totalRG12 = 0;
                $totalRG24 = 0;
                $totalRG12F = 0;
                $totalRG24F = 0;
            }

            $saldo = $rResultSaldosRG[0]['saldo'] - ($totalRG12 * 12) - ($totalRG24 * 24) - ($totalRG12F * 12) - ($totalRG24F * 24);

            //////////////////////  FIN ///////////////////////////////////


            //////          Traigo los dias de Guardi del Agente  /////////////////
            $sqlDiasGuardias = 'EXEC haberes.haberes.spDiasGuardiaWeb '.$entity->getIdPersonalCargo();
            /*$stmt = $this   ->getDoctrine()
                            ->getManager('odbc_haberes')
                            ->getConnection()
                            ->prepare($sql);
            $rResultReemplazo = $stmt->fetch();
            */
            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmtDiasGuardias = $conn->prepare($sqlDiasGuardias);

            $stmtDiasGuardias->execute();
            $rResultDiasGuardias = 0;
            $rResultDiasGuardias = $stmtDiasGuardias->fetchAll();
            /////                  FIN                            /////////////////

            $rResultDiasGuardiasRG = 0;
            //////          Traigo los dias de Guardi del Agente  /////////////////
            if (($this->getRequest()->getSession()->get('refpersonalReemplazo')!=0)) {

                $sqlDiasGuardiasRG = 'EXEC haberes.haberes.spDiasGuardiaWeb '.$this->getRequest()->getSession()->get('refpersonalReemplazo');
                /*$stmt = $this   ->getDoctrine()
                                ->getManager('odbc_haberes')
                                ->getConnection()
                                ->prepare($sql);
                $rResultReemplazo = $stmt->fetch();
                */
                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmtDiasGuardiasRG = $conn->prepare($sqlDiasGuardiasRG);

                $stmtDiasGuardiasRG->execute();
                $rResultDiasGuardiasRG = 0;
                $rResultDiasGuardiasRG = $stmtDiasGuardiasRG->fetchAll();
            }
            /////                  FIN                            /////////////////

            $em =  $this->getDoctrine()->getManager("ms_haberes_web");

            $novedades   =  $em->getRepository('LiquidacionesParteNovedadesBundle:Novedad')->findAll();

            //die(var_dump($entity->getRGIdNovedad()));
            $editForm = $this->createEditForm($entity,$modo);
            $deleteForm = $this->createDeleteForm($id,$entity->getCuposhatipoliquidacion()->getId());

            return array(
                'entity' => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'resultados' => $rResult,
                'resultadosReemplazo' => $rResultReemp,
                'cupo' => $entity->getCuposhatipoliquidacion()->getCupos()->getId(),
                'cupototal'=>$entity->getCuposhatipoliquidacion()->getCupos()->getMonto(),
                'cupogastado'=>$totalGastadoCupoMensual,
                'rg12' => $totalRG12 + ($gastado12hs/12),
                'rg24' => $totalRG24 + ($gastado24hs/24),
                'rg12f'=> $totalRG12F + ($gastado12hsferiado/12),
                'rg24f'=> $totalRG24F + ($gastado24hsferiado/24),
                'conceptoValor24hs'=>$concepto24hs,
                'conceptoValor12hs'=>$concepto12hs,
                'conceptoValor24hsFeriado'=>$concepto24hsferiado,
                'conceptoValor12hsFeriado'=>$concepto12hsferiado,
                'diasGuardias'=>$rResultDiasGuardias,
                'diasGuardiasRG'=>$rResultDiasGuardiasRG,
                'mes'=>$mes,
                'anio'=>$anio,
                'saldo' => $saldo + $horasDevolver,
                'novedad' => $novedades,
                'novedadCargada' => $entity->getRGIdNovedad(),
                'fechanovedad'=>$entity->getRGFecha(),
                'idHACTL'=>$entity->getCuposhatipoliquidacion()->getId(),
            );

        } else {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));

        }

    }



    /**
     * Displays a form to edit an existing Liquidaciones entity.
     *
     * @Route("/{id}/edithsinvestigacion", name="liquidaciones_edithsinvestigacion")
     * @Method("GET")
     * @Template()
     */
    public function edithsinvestigacionAction($id)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Liquidaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Liquidaciones entity.');
        }

        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_22'))
                ){


            //busco el modo de carga para saber en que formulario entra
            $modo = $entity->getCuposhatipoliquidacion()->getCuentas()->getModoCarga();

            //miro que tipo de liquidacion es para diferenciar ART48 y REEMPLAZO
            $idtipoliquidacion = $entity->getCuposhatipoliquidacion()->getCuentas()->getIdTipoLiquidacion();

            // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
            if ($this->cupoCerradoPorId($entity->getCuposhatipoliquidacion()->getId()) == true) {
                $direccion = "liquidaciones_cuposliquidacion";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
                );
                return $this->redirect($this->generateUrl($direccion));
            }
            ////////////////////////// FIN  ////////////////////////////////////////////////////
            //
            //////////////////  BUSCO LOS AGENTES CARGADOS  //////////////////
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$entity->getIdPersonalCargo().',4';
                    /*$stmt = $this   ->getDoctrine()
                                    ->getManager('odbc_haberes')
                                    ->getConnection()
                                    ->prepare($sql);

                    $rResult = 0;
                    $rResult = $stmt->fetch();*/
                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmt = $conn->prepare($sql);

                $stmt->execute();
                $rResult = 0;

                $rResult = $stmt->fetchAll();
            ///////////////////////////////////////// FIN //////////////////////////

            ////////// Reconosco Fechas   /////////////////////////////////////
            $anio = $entity->getCuposhatipoliquidacion()->getCupos()->getAnio();
            $mes = $entity->getCuposhatipoliquidacion()->getCupos()->getMes();
            ////////////////////// FIN   /////////////////////////////

            /////////////////////// PARA LOS ACUMULADORES /////////////////



            $totalHS = $em->createQuery('SELECT sum(l.hsExCantSimples) as hssimples,sum(l.hsExCantDobles) as hsdobles FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      JOIN ca.cupos c
                      WHERE (l.id <> :idCAH and c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('idCAH'=> $id, 'Anio' => $anio, 'Mes'=> $mes,'idPersonalC'=> $entity->getIdPersonalCargo(),))->getResult();

            //die(var_dump($totalHS));
            if ($totalHS != null) {
                $totalHsSimples = $totalHS[0]["hssimples"] == null ? 0 : $totalHS[0]["hssimples"];
                $totalHsDobles = $totalHS[0]["hsdobles"] == null ? 0 : $totalHS[0]["hsdobles"];
            } else {
                $totalHsSimples = 0;
                $totalHsDobles = 0;
            }


            ////////////////////////  FIN ACUMULADORES //////////////////////////////

            //traigo el valor hora del agente
            $mes = (integer)date('m');
            $anio = (integer)date('Y');

            $refregimenestatutario = $rResult[0]["IdRegimenEstatutario"];
            $refagrupamiento = $rResult[0]["IdAgrupamiento"];
            $refencasillamiento = $rResult[0]["IdCodEncasillamiento"];

            $sqlVH = "select
              tm.modulo, v.monto
              from        LiquidacionesWeb.dbo.TipoModulos tm
              inner
              join        LiquidacionesWeb.dbo.ValorModulo v
              on          v.reftipomodulos = tm.id";
            /*$stmtVH = $this   ->getDoctrine()
                            ->getManager('odbc_haberes')
                            ->getConnection()
                            ->prepare($sqlVH);

            $rResultVH = 0;

            $rResultVH = $stmtVH->fetch();*/

            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmt = $conn->prepare($sqlVH);

            $stmt->execute();
            $rResultVH = 0;

            $rResultVH = $stmt->fetchAll();

            if ($rResultVH == null) {
                $sqlValidacionModulos = "select
                'No se puede cargar' as modulo, 0 as monto
                ";

                $stmt = $conn->prepare($sqlValidacionModulos);

                $stmt->execute();
                $rResultVH = 0;

                $rResultVH = $stmt->fetchAll();

            }

            $editForm = $this->createEditForm($entity,$modo);
            $deleteForm = $this->createDeleteForm($id,$entity->getCuposhatipoliquidacion()->getId());

            return array(
               'entity' => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'resultados' => $rResult,
                'cupo' => $entity->getCuposhatipoliquidacion()->getCupos()->getId(),
                'cupototal'=>$entity->getCuposhatipoliquidacion()->getCupos()->getMonto(),
                'valorhora'=>  $rResultVH,
                'horassimples'=> $totalHsSimples,
                'horasdobles'=>$totalHsDobles,
                'idHACTL'=>$entity->getCuposhatipoliquidacion()->getId(),
            );

        } else {

            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('liquidaciones_cuposliquidacion'));

        }
    }


    /**
     * Displays a form to edit an existing Liquidaciones entity.
     *
     * @Route("/{id}/ediths", name="liquidaciones_ediths")
     * @Method("GET")
     * @Template()
     */
    public function edithsAction($id)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Liquidaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Liquidaciones entity.');
        }

        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_9')) ||
            (true === $securityContext->isGranted('ROLE_10')) || (true === $securityContext->isGranted('ROLE_11')) ||
            (true === $securityContext->isGranted('ROLE_12')) || (true === $securityContext->isGranted('ROLE_13')) ||
            (true === $securityContext->isGranted('ROLE_14')) || (true === $securityContext->isGranted('ROLE_16')) ||
            (true === $securityContext->isGranted('ROLE_20')) || (true === $securityContext->isGranted('ROLE_17'))
                ){


            //busco el modo de carga para saber en que formulario entra
            $modo = $entity->getCuposhatipoliquidacion()->getCuentas()->getModoCarga();

            //miro que tipo de liquidacion es para diferenciar ART48 y REEMPLAZO
            $idtipoliquidacion = $entity->getCuposhatipoliquidacion()->getCuentas()->getIdTipoLiquidacion();

            // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
            if ($this->cupoCerradoPorId($entity->getCuposhatipoliquidacion()->getId()) == true) {
                $direccion = "liquidaciones_cuposliquidacion";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
                );
                return $this->redirect($this->generateUrl($direccion));
            }
            ////////////////////////// FIN  ////////////////////////////////////////////////////
            //
            //////////////////  BUSCO LOS AGENTES CARGADOS  //////////////////
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$entity->getIdPersonalCargo().',4';
                    /*$stmt = $this   ->getDoctrine()
                                    ->getManager('odbc_haberes')
                                    ->getConnection()
                                    ->prepare($sql);

                    $rResult = 0;
                    $rResult = $stmt->fetch();*/
                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmt = $conn->prepare($sql);

                $stmt->execute();
                $rResult = 0;

                $rResult = $stmt->fetchAll();
            ///////////////////////////////////////// FIN //////////////////////////

            ////////// Reconosco Fechas   /////////////////////////////////////
            $anio = $entity->getCuposhatipoliquidacion()->getCupos()->getAnio();
            $mes = $entity->getCuposhatipoliquidacion()->getCupos()->getMes();
            ////////////////////// FIN   /////////////////////////////

            /////////////////////// PARA LOS ACUMULADORES /////////////////

            $total = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $entity->getCuposhatipoliquidacion()->getCupos()->getId())->getResult();

            if ($total != null) {
                $totalGastadoCupoMensual = $total[0]["total"] == 0 ? 0 : $total[0]["total"];
            } else {
                $totalGastadoCupoMensual = 0;
            }

            $totalHS = $em->createQuery('SELECT sum(l.hsExCantSimples) as hssimples,sum(l.hsExCantDobles) as hsdobles FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      JOIN ca.cupos c
                      WHERE (l.id <> :idCAH and c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('idCAH'=> $id, 'Anio' => $anio, 'Mes'=> $mes,'idPersonalC'=> $entity->getIdPersonalCargo(),))->getResult();

            //die(var_dump($totalHS));
            if ($totalHS != null) {
                $totalHsSimples = $totalHS[0]["hssimples"] == null ? 0 : $totalHS[0]["hssimples"];
                $totalHsDobles = $totalHS[0]["hsdobles"] == null ? 0 : $totalHS[0]["hsdobles"];
            } else {
                $totalHsSimples = 0;
                $totalHsDobles = 0;
            }


            ////////////////////////  FIN ACUMULADORES //////////////////////////////

            //traigo el valor hora del agente
            $mes = (integer)date('m');
            $anio = (integer)date('Y');

            $sqlVH = "exec haberes.haberes.spTraerValorHoraLiquidaciones ".$mes.",".$anio.",".$entity->getCuposhatipoliquidacion()->getCupos()->getId();
            /*$stmtVH = $this   ->getDoctrine()
                            ->getManager('odbc_haberes')
                            ->getConnection()
                            ->prepare($sqlVH);

            $rResultVH = 0;

            $rResultVH = $stmtVH->fetch();*/

            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $rResultVH = 0;

            $rResultVH = $stmt->fetchAll();

            $editForm = $this->createEditForm($entity,$modo);
            $deleteForm = $this->createDeleteForm($id,$entity->getCuposhatipoliquidacion()->getId());

            return array(
               'entity' => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'resultados' => $rResult,
                'cupo' => $entity->getCuposhatipoliquidacion()->getCupos()->getId(),
                'cupototal'=>$entity->getCuposhatipoliquidacion()->getCupos()->getMonto(),
                'cupogastado'=>$totalGastadoCupoMensual,
                'valorhora'=>  number_format($entity->getHsExValorHora(),2),
                'horassimples'=> $totalHsSimples,
                'horasdobles'=>$totalHsDobles,
                'idHACTL'=>$entity->getCuposhatipoliquidacion()->getId(),
            );

        } else {

            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('liquidaciones_cuposliquidacion'));

        }
    }


    /**
     * Displays a form to edit an existing Liquidaciones entity.
     *
     * @Route("/{id}/editmonto", name="liquidaciones_editmonto")
     * @Method("GET")
     * @Template()
     */
    public function editmontoAction($id)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Liquidaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Liquidaciones entity.');
        }

        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_9')) ||
            (true === $securityContext->isGranted('ROLE_10')) || (true === $securityContext->isGranted('ROLE_11')) ||
            (true === $securityContext->isGranted('ROLE_12')) || (true === $securityContext->isGranted('ROLE_13')) ||
            (true === $securityContext->isGranted('ROLE_14')) || (true === $securityContext->isGranted('ROLE_16')) ||
            (true === $securityContext->isGranted('ROLE_20')) || (true === $securityContext->isGranted('ROLE_17'))
                ){


            //busco el modo de carga para saber en que formulario entra
            $modo = $entity->getCuposhatipoliquidacion()->getCuentas()->getModoCarga();

            //miro que tipo de liquidacion es para diferenciar ART48 y REEMPLAZO
            $idtipoliquidacion = $entity->getCuposhatipoliquidacion()->getCuentas()->getIdTipoLiquidacion();

            // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
            if ($this->cupoCerradoPorId($entity->getCuposhatipoliquidacion()->getId()) == true) {
                $direccion = "liquidaciones_cuposliquidacion";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
                );
                return $this->redirect($this->generateUrl($direccion));
            }
            ////////////////////////// FIN  ////////////////////////////////////////////////////
            //
            //////////////////  BUSCO LOS AGENTES CARGADOS  //////////////////
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$entity->getIdPersonalCargo().',4';
                    /*$stmt = $this   ->getDoctrine()
                                    ->getManager('odbc_haberes')
                                    ->getConnection()
                                    ->prepare($sql);

                    $rResult = 0;
                    $rResult = $stmt->fetch();*/

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                $stmt = $conn->prepare($sql);

                $stmt->execute();
                $rResult = 0;

                $rResult = $stmt->fetchAll();
            ///////////////////////////////////////// FIN //////////////////////////

            ////////// Reconosco Fechas   /////////////////////////////////////
            $anio = $entity->getCuposhatipoliquidacion()->getCupos()->getAnio();
            $mes = $entity->getCuposhatipoliquidacion()->getCupos()->getMes();
            ////////////////////// FIN   /////////////////////////////

            /////////////////////// PARA LOS ACUMULADORES /////////////////

            $total = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $entity->getCuposhatipoliquidacion()->getCupos()->getId())->getResult();

            if ($total != null) {
                $totalGastadoCupoMensual = $total[0]["total"] == 0 ? 0 : $total[0]["total"];
            } else {
                $totalGastadoCupoMensual = 0;
            }


            $editForm = $this->createEditForm($entity,$modo);
            $deleteForm = $this->createDeleteForm($id,$entity->getCuposhatipoliquidacion()->getId());

            return array(
                'entity' => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'resultados' => $rResult,
                'hacupo' =>$entity->getCuposhatipoliquidacion()->getId(),
                'cupo' => $entity->getCuposhatipoliquidacion()->getCupos()->getId(),
                'cupototal'=>$entity->getCuposhatipoliquidacion()->getCupos()->getMonto(),
                'cupogastado'=>$totalGastadoCupoMensual,
                'montoCargado' => $entity->getMontoTotalCalculado(),
                'idHACTL'=>$entity->getCuposhatipoliquidacion()->getId(),
            );

        } else {

            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('liquidaciones_cuposliquidacion'));

        }
    }
    /**
    * Creates a form to edit a Liquidaciones entity.
    *
    * @param Liquidaciones $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Liquidaciones $entity, $modo)
    {
        //$tipoguardia = array(515=>'12',514=>'24',517=>'12 Hs Feriado',516=>'24 Hs Feriado');
        /////   Verifico si la fecha es un feriado   //////////////////
    	if ($modo == 'rg') {
        if ($entity->getRGFecha() != null) {


	        $sqlFeriados = "EXEC haberes.web.spTraerFeriadoPorFecha_Dependencia '".$entity->getRGFecha()->format('Y-m-d')."',".$entity->getCuposhatipoliquidacion()->getCupos()->getIdDependencia();

	        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

	        $stmtFeriados = $conn->prepare($sqlFeriados);

	        $stmtFeriados->execute();
	        $rResultFeriados = 0;
	        $rResultFeriados = $stmtFeriados->fetchAll();
	    } else {
	    	$rResultFeriados = 0;
	    }
        //die(var_dump($rResultFeriados));
        //////            FIN                        ///////////////////


        /////   Verifico la cantidad de horas que me quedan segun las guardias del agente y del reemplazado //////
        $sqlSaldoHoras = 'EXEC haberes.haberes.spValidaReemplazoMismoDiaSemana '.$entity->getRGIdPersonalCargo().','.$entity->getIdPersonalCargo().','.$entity->getRGFecha()->format('N').",'".$entity->getRGFecha()->format('Y-m-d')."'";

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

        $stmtSaldoHoras = $conn->prepare($sqlSaldoHoras);

        $stmtSaldoHoras->execute();
        $rResultSaldoHoras = 0;
        $rResultSaldoHoras = $stmtSaldoHoras->fetchAll();
        //die(var_dump($rResultSaldoHoras[0]['computed']));
        $valores = explode("/", $rResultSaldoHoras[0]['computed']);

        $observacionConcepto = '';
        $tipoguardia = array(0=>'No posee horas');
        if ($valores[0]==0) {
            $observacionConcepto = $valores[1];
        } else {

            $observacionConcepto = "Horas Disponibles en fecha elegida: ".(abs($valores[1])==0 ? '24' : abs($valores[1]))." hs";


            if (($entity->getRGFecha()->format('N') == 6) || ($entity->getRGFecha()->format('N') == 7) || ($rResultFeriados[0]['computed'] == 1)) {
                switch (abs($valores[1])) {
                    case 24:
                        $tipoguardia = array(517=>'12 Hs Feriado',516=>'24 Hs Feriado');

                        break;
                    case 12:
                       $tipoguardia = array(517=>'12 Hs Feriado');
                        break;
                    case 0:
                        $tipoguardia = array(517=>'12 Hs Feriado');

                        break;
                    default:
                        $tipoguardia = array(0=>'No posee horas');
                        break;
                }
            } else {
                switch (abs($valores[1])) {
                    case 24:
                        $tipoguardia = array(515=>'12 Hs',514=>'24 Hs');

                        break;
                    case 12:
                        $tipoguardia = array(515=>'12 Hs');
                        break;
                    case 0:
                        $tipoguardia = array(515=>'12 Hs',514=>'24 Hs');

                        break;
                    default:
                        $tipoguardia = array(0=>'No posee horas');
                        break;
                }
            }

        }
        }
        // RG invalido: Hace guardia ese dia

        // Solo puede 12 horas

        //die(var_dump($rResultSaldoHoras));

        /////                   FIN                             ////////////////////////////////
        switch ($modo) {
            case 'rg':
                $form = $this->createForm(new \Liquidaciones\CuposAnualesBundle\Form\LiquidacionesType($tipoguardia), $entity, array(
                    'action' => $this->generateUrl('liquidaciones_update', array('id' => $entity->getId())),
                    'method' => 'PUT',
                ));
                break;
            case 'horas':
                $form = $this->createForm(new \Liquidaciones\CuposAnualesBundle\Form\LiquidacionesTypeHS(), $entity, array(
                    'action' => $this->generateUrl('liquidaciones_update', array('id' => $entity->getId())),
                    'method' => 'PUT',
                ));

                break;
            case 'monto':
                $form = $this->createForm(new \Liquidaciones\CuposAnualesBundle\Form\LiquidacionesTypeM(), $entity, array(
                    'action' => $this->generateUrl('liquidaciones_update', array('id' => $entity->getId())),
                    'method' => 'PUT',
                ));

                break;
            default:
                throw $this->createNotFoundException('Unable to find Liquidaciones entity.');
                break;
        }


        //$form->add('submit', 'submit', array('label' => 'Modificar'));

        return $form;
    }
    /**
     * Edits an existing Liquidaciones entity.
     *
     * @Route("/update/{id}", name="liquidaciones_update")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Liquidaciones:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Liquidaciones')->find($id);
        $rGFechaAux = $entity->getRGFecha();



        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Liquidaciones entity.');
        }

        //busco el modo de carga para saber en que formulario entra
        $modo = $entity->getCuposhatipoliquidacion()->getCuentas()->getModoCarga();

        //miro que tipo de liquidacion es para diferenciar ART48 y REEMPLAZO
        $idtipoliquidacion = $entity->getCuposhatipoliquidacion()->getCuentas()->getIdTipoLiquidacion();

        //verifico que no sea RG pago de deuda
        $idCuenta          = $entity->getCuposhatipoliquidacion()->getCuentas()->getId();

        //guardo el idpersona para setearlo despues
        $personalcargo  = $entity->getIdPersonalCargo();
        $resCUHA        = $entity->getCuposhatipoliquidacion()->getId();

        //busco si la session me trajo el reemplazado
        $session = $this->getRequest()->getSession();
        $refRGpersonalcargo = (integer)$this->getRequest()->getSession()->get('refpersonalReemplazo');

        $data = $request->request->all();

        if (isset($data['motivos'])) {
            $rGIdNovedad     = $data['motivos'];
        } else {
            $rGIdNovedad     = 0;
        }

        $idpersonalcargo = (integer)$this->getRequest()->getSession()->get('personal');
        $entity->setIdPersonalCargo($idpersonalcargo);
        $entity->setRGIdNovedad($rGIdNovedad);

        // si quiere entrar por una url para cargar verifico que el cupo este cerrado o no.
        if ($this->cupoCerradoPorId($entity->getCuposhatipoliquidacion()->getId()) == true) {
            $direccion = "liquidaciones_cuposliquidacion";
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'El Cupo no se encuentra disponible para poder cargar datos. "El Cupo debe estar ABIERTO"'
            );
            return $this->redirect($this->generateUrl($direccion));
        }
        ////////////////////////// FIN  ////////////////////////////////////////////////////

        $anio = $entity->getCuposhatipoliquidacion()->getCupos()->getAnio();
        $mes = $entity->getCuposhatipoliquidacion()->getCupos()->getMes();

        ////   BINDEO los datos traidos, pero me guardo los montos anteriores para comparar  ////////////////////////////////////////
        $montoAnterior = $entity->getMontoTotalCalculado();
        $deleteForm = $this->createDeleteForm($id,$entity->getCuposhatipoliquidacion()->getId());
        $hssimplesAnterior = $entity->getHsExCantSimples();
        $hsdoblesAnterior = $entity->getHsExCantDobles();


        $editForm = $this->createEditForm($entity,$modo);
        $editForm->bind($request);

        $entity->setRGFecha($rGFechaAux);
        //die(var_dump($entity->getRGFecha()));
        ///////////////////////  FIN /////////////////////////////////////////////

        switch ($modo) {
            case 'rg':

                if (($entity->getMontoTotalCalculado() == 0) || ($entity->getMontoTotalCalculado()== null)) {
                    //die(var_dump($persona));
                    if ($idCuenta != 24) {
                        $direccion = "liquidaciones_edit";
                    } else {
                        $direccion = "liquidaciones_editdeuda";
                    }
                    $this->getRequest()->getSession()->getFlashBag()->add(
                        'aviso_error',
                        'El monto total debe ser mayor a cero. '
                    );
                    return $this->redirect($this->generateUrl($direccion,array('id'=>$id,'refRGpersonalcargo'=>$refRGpersonalcargo)));
                }

                if (($entity->getRGCantHsGuardia() <= 0) || ($entity->getRGCantHsGuardia()== null)) {
                    //die(var_dump($persona));
                    if ($idCuenta != 24) {
                        $direccion = "liquidaciones_edit";
                    } else {
                        $direccion = "liquidaciones_editdeuda";
                    }
                    $this->getRequest()->getSession()->getFlashBag()->add(
                        'aviso_error',
                        'La cantidad cargada no puede ser cero o negativa. '
                    );
                    return $this->redirect($this->generateUrl($direccion,array('id'=>$id,'refRGpersonalcargo'=>$refRGpersonalcargo)));
                }

                if (($this->devolverSaldo($entity->getCuposhatipoliquidacion()->getCupos()->getId()) + $montoAnterior - $entity->getMontoTotalCalculado()) < 0) {
                    if ($idCuenta != 24) {
                        $direccion = "liquidaciones_edit";
                    } else {
                        $direccion = "liquidaciones_editdeuda";
                    }
                    $this->getRequest()->getSession()->getFlashBag()->add(
                        'aviso_error',
                        'El monto total no debe superar al cupo. '
                    );
                    return $this->redirect($this->generateUrl($direccion,array('id'=>$id,'refRGpersonalcargo'=>$refRGpersonalcargo)));
                }

                $valorHoras = 0;
                // controlo si sobrepasa las vacantes siempre y cuando no cargo un reemplazado
                switch ($entity->getIdConcepto()) {
                    case '515':
                        $valorHoras = 12;
                        break;
                    case '514':
                        $valorHoras = 24;
                        break;
                    case '517':
                        $valorHoras = 12;
                        break;
                    case '516':
                        $valorHoras = 24;
                        break;
                }
                if ((
                    ($refRGpersonalcargo == '' ? 0 : $refRGpersonalcargo) == 0) && ( ($this->devolverVacantesTotalesHoras($entity->getCuposhatipoliquidacion()->getCupos()->getId()) - $this->devolverTotalHorasRGUpdate($entity->getCuposhatipoliquidacion()->getCupos()->getId(), $entity->getId()) - $valorHoras) < 0)) {

                    if ($idCuenta != 24) {
                        $direccion = "liquidaciones_edit";
                    } else {
                        $direccion = "liquidaciones_editdeuda";
                    }
                    $this->getRequest()->getSession()->getFlashBag()->add(
                        'aviso_error',
                        'Ha superado la cantidad de Vacantes Mensuales. '
                    );
                    return $this->redirect($this->generateUrl($direccion,array('id'=>$id,'refRGpersonalcargo'=>$refRGpersonalcargo)));

                }

                //die(var_dump($this->devolverVacantesTotalesHoras($cuposhatl->getCupos()->getId()) - $this->devolverTotalHorasRG($cuposhatl->getCupos()->getId()) - $valorHoras));


                if ($idtipoliquidacion == 40) {
                    switch ($entity->getIdConcepto()) {
                        case '515':
                            $entity->setIdConcepto(365);
                            break;
                        case '514':
                            $entity->setIdConcepto(364);
                            break;
                        case '517':
                            $entity->setIdConcepto(367);
                            break;
                        case '516':
                            $entity->setIdConcepto(366);
                            break;
                    }
                    $totalRG = $em->createQuery('SELECT
                                    sum((case when l.idConcepto = 365 then l.rGCantHsGuardia else 0 end)) as docehs,
                                    sum((case when l.idConcepto = 364 then l.rGCantHsGuardia else 0 end)) as veinticuatrohs,
                                    sum((case when l.idConcepto = 367 then l.rGCantHsGuardia else 0 end)) as docehsferiado,
                                    sum((case when l.idConcepto = 366 then l.rGCantHsGuardia else 0 end)) as veinticutrohsferiado
                                FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                              JOIN l.cuposhatipoliquidacion ca
                              JOIN ca.cupos c
                              WHERE (c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('Anio' => $anio, 'Mes'=> $mes,'idPersonalC'=> $personalcargo))->getResult();

                } else {
                    $totalRG = $em->createQuery('SELECT
                                    sum((case when l.idConcepto = 515 then l.rGCantHsGuardia else 0 end)) as docehs,
                                    sum((case when l.idConcepto = 514 then l.rGCantHsGuardia else 0 end)) as veinticuatrohs,
                                    sum((case when l.idConcepto = 517 then l.rGCantHsGuardia else 0 end)) as docehsferiado,
                                    sum((case when l.idConcepto = 516 then l.rGCantHsGuardia else 0 end)) as veinticutrohsferiado
                                FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                              JOIN l.cuposhatipoliquidacion ca
                              JOIN ca.cupos c
                              WHERE (c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('Anio' => $anio, 'Mes'=> $mes,'idPersonalC'=>$personalcargo))->getResult();

                }



                if ($totalRG != null) {
                    $totalRG12 = $totalRG[0]["docehs"] == null ? 0 : $totalRG[0]["docehs"];
                    $totalRG24 = $totalRG[0]["veinticuatrohs"] == null ? 0 : $totalRG[0]["veinticuatrohs"];
                    $totalRG12F = $totalRG[0]["docehsferiado"] == null ? 0 : $totalRG[0]["docehsferiado"];
                    $totalRG24F = $totalRG[0]["veinticutrohsferiado"] == null ? 0 : $totalRG[0]["veinticutrohsferiado"];

                } else {
                    $totalRG12 = 0;
                    $totalRG24 = 0;
                    $totalRG12F = 0;
                    $totalRG24F = 0;
                }
                //////////////////  BUSCO LOS AGENTES CARGADOS  //////////////////
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$personalcargo.',4';
                /*$stmt = $this   ->getDoctrine()
                                ->getManager('odbc_haberes')
                                ->getConnection()
                                ->prepare($sql);

                $rResult = 0;
                $rResult = $stmt->fetch();*/

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $rResult = 0;

                $rResult = $stmt->fetchAll();

                if ($entity->getRGIdPersonalCargo() != null) {
                    $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.($entity->getRGIdPersonalCargo() == '' ? 0 : $entity->getRGIdPersonalCargo()).',4';
                    /*$stmt = $this   ->getDoctrine()
                                    ->getManager('odbc_haberes')
                                    ->getConnection()
                                    ->prepare($sql);

                    $rResultReemp = $stmt->fetch();*/

                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $rResultReemp = 0;

                    $rResultReemp = $stmt->fetchAll();
                } else {
                    if ($refRGpersonalcargo != null) {
                        $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$refRGpersonalcargo.',4';
                        /*$stmt = $this   ->getDoctrine()
                                        ->getManager('odbc_haberes')
                                        ->getConnection()
                                        ->prepare($sql);
                        $rResultReemp = $stmt->fetch();*/

                        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $rResultReemp = 0;

                        $rResultReemp = $stmt->fetchAll();
                    } else {
                        $rResultReemp = 0;
                    }
                }

                $total = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                          JOIN l.cuposhatipoliquidacion ca
                          WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $entity->getCuposhatipoliquidacion()->getCupos()->getId())->getResult();

                if ($total != null) {
                    $totalGastadoCupoMensual = $total[0]["total"] == 0 ? 0 : $total[0]["total"];
                } else {
                    $totalGastadoCupoMensual = 0;
                }
                //////////////////  FIN  //////////////////
                //////////////////  BUSCO LOS AGENTES CARGADOS  //////////////////


                //////////////////  FIN  //////////////////
                $entity->setRGIdPersonalCargo($refRGpersonalcargo);
                if ($refRGpersonalcargo == 0) {
                    /*
                    if ((($idtipoliquidacion == 40) || ($idtipoliquidacion == 24)) && ($usuarioAutoriza == 0)) {
                        $direccion = "liquidaciones_edit";
                        $this->getRequest()->getSession()->getFlashBag()->add(
                            'aviso_error',
                            'Es obligatorio cargar el agente que será reemplazado.'
                        );
                        return $this->redirect($this->generateUrl($direccion,array('id'=>$id,'refRGpersonalcargo'=>$refRGpersonalcargo)));
                    } else {
                        $entity->setRequiereAutorizacion(1);
                    }*/
                    $entity->setRequiereAutorizacion(0);
                } else {

                    $entity->setRequiereAutorizacion(0);
                }

                break;
            case 'horas':

                $entity->setIdPersonalCargo($personalcargo);

                if ((($entity->getHsExCantSimples() <= 0) || ($entity->getHsExCantSimples()== null)) && (($entity->getHsExCantDobles() <= 0) || ($entity->getHsExCantDobles()== null))) {
                    //die(var_dump($persona));
                    if ($idCuenta == 26) {
                        $direccion = "liquidaciones_edithsinvestigacion";
                    } else {
                        $direccion = "liquidaciones_ediths";
                    }

                    $this->getRequest()->getSession()->getFlashBag()->add(
                        'aviso_error',
                        'La cantidad cargada no puede ser cero o negativa. '
                    );
                    return $this->redirect($this->generateUrl($direccion,array('id'=>$id)));
                }

                if (($entity->getMontoTotalCalculado() == 0) || ($entity->getMontoTotalCalculado()== null)) {
                    if ($idCuenta == 26) {
                        $direccion = "liquidaciones_edithsinvestigacion";
                    } else {
                        $direccion = "liquidaciones_ediths";
                    }
                    $this->getRequest()->getSession()->getFlashBag()->add(
                        'aviso_error',
                        'El monto total debe ser mayor a cero. '
                    );
                    return $this->redirect($this->generateUrl($direccion,array('id'=>$id)));
                }

                if ($idCuenta != 26) {
                    if (($this->devolverSaldo($entity->getCuposhatipoliquidacion()->getCupos()->getId()) + $montoAnterior - $entity->getMontoTotalCalculado()) < 0) {
                        $direccion = "liquidaciones_ediths";
                        $this->getRequest()->getSession()->getFlashBag()->add(
                            'aviso_error',
                            'El monto total no debe superar al cupo. '
                        );
                        return $this->redirect($this->generateUrl($direccion,array('id'=>$id)));
                    }
                }


                $valorT = round($entity->getMontoTotalCalculado(),2);

                $valorS = round((round($entity->getHsExValorHora(),2) * 1.5 * $entity->getHsExCantSimples()),2);

                $valorD = round((round($entity->getHsExValorHora(),2) * 2 * $entity->getHsExCantDobles()),2);

                $valorSD = round($valorS,2, PHP_ROUND_HALF_DOWN) + round($valorD,2, PHP_ROUND_HALF_DOWN);

                $valorDif = round($valorT,2, PHP_ROUND_HALF_DOWN) - round($valorSD,2, PHP_ROUND_HALF_DOWN);

                if ($idCuenta != 26) {
                    if (($valorDif < -0.09) || ($valorDif > 0.09)) {
                        $direccion = "liquidaciones_ediths";
                        $this->getRequest()->getSession()->getFlashBag()->add(
                            'aviso_error',
                            'El monto total no coincide con las cantidades. '
                        );
                        return $this->redirect($this->generateUrl($direccion,array('id'=>$id)));

                    }
                }


                //// nuevo 2020-09-03 ///////////////////////////////////////////////////////////
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$personalcargo.',4';

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $rResult = 0;

                $rResult = $stmt->fetchAll();

                $rRegimenHorario = 0;
                $rRegimenHorario = $rResult[0]['RegHorario'];
                /////////////////////////////////////////////////////////////////////////////////
                //////////////////// valido que no supere sus horas /////////////////////////////
                $totalHSV = $em->createQuery('SELECT sum(l.hsExCantSimples) as hssimples,sum(l.hsExCantDobles) as hsdobles FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                              JOIN l.cuposhatipoliquidacion ca
                              JOIN ca.cupos c
                              WHERE (ca.refCuenta not in (1,2,3,4,3,18,18,19) and c.anio= :Anio and c.mes = :Mes AND l.idPersonalCargo = :idPersonalC)')->setParameters(array('Anio' => $anio, 'Mes'=> $mes,'idPersonalC'=> $personalcargo,))->getResult();

                //die(var_dump($totalHS));
                if ($totalHSV != null) {
                    $totalHsSimples = $totalHSV[0]["hssimples"] == null ? 0 : $totalHSV[0]["hssimples"];
                    $totalHsDobles = $totalHSV[0]["hsdobles"] == null ? 0 : $totalHSV[0]["hsdobles"];
                } else {
                    $totalHsSimples = 0;
                    $totalHsDobles = 0;
                }

                if ($idCuenta != 26) {
                    if ($rRegimenHorario == 30) {
                        if ( ($totalHsSimples - $hssimplesAnterior + $entity->getHsExCantSimples()) > 120) {
                            $direccion = "liquidaciones_ediths";
                            $this->getRequest()->getSession()->getFlashBag()->add(
                                'aviso_error',
                                'Las horas simples superan el cupo mensual de 120 horas.'
                            );
                            return $this->redirect($this->generateUrl($direccion,array('id'=>$id)));
                        }

                        if ( ($totalHsDobles - $hsdoblesAnterior + $entity->getHsExCantDobles()) > 40) {
                            $direccion = "liquidaciones_ediths";
                            $this->getRequest()->getSession()->getFlashBag()->add(
                                'aviso_error',
                                'Las horas dobles superan el cupo mensual de 40 horas.'
                            );
                            return $this->redirect($this->generateUrl($direccion,array('id'=>$id)));
                        }
                    } else {
                        if ( ($totalHsSimples - $hssimplesAnterior + $entity->getHsExCantSimples()) > 80) {
                            $direccion = "liquidaciones_ediths";
                            $this->getRequest()->getSession()->getFlashBag()->add(
                                'aviso_error',
                                'Las horas simples superan el cupo mensual de 80 horas.'
                            );
                            return $this->redirect($this->generateUrl($direccion,array('id'=>$id)));
                        }

                        if ( ($totalHsDobles - $hsdoblesAnterior + $entity->getHsExCantDobles()) > 40) {
                            $direccion = "liquidaciones_ediths";
                            $this->getRequest()->getSession()->getFlashBag()->add(
                                'aviso_error',
                                'Las horas dobles superan el cupo mensual de 40 horas.'
                            );
                            return $this->redirect($this->generateUrl($direccion,array('id'=>$id)));
                        }
                    }
                }

                /////////////////////////////////////////////////////////////////////////////////

                break;
            case 'monto':

                $entity->setIdPersonalCargo($personalcargo);

                if (($entity->getMontoTotalCalculado() == 0) || ($entity->getMontoTotalCalculado()== null)) {
                    $direccion = "liquidaciones_ediths";
                    $this->getRequest()->getSession()->getFlashBag()->add(
                        'aviso_error',
                        'El monto total debe ser mayor a cero. '
                    );
                    return $this->redirect($this->generateUrl($direccion,array('id'=>$id)));
                }

                if (($this->devolverSaldo($entity->getCuposhatipoliquidacion()->getCupos()->getId()) + $montoAnterior - $entity->getMontoTotalCalculado()) < 0) {
                    $direccion = "liquidaciones_editmonto";
                    $this->getRequest()->getSession()->getFlashBag()->add(
                        'aviso_error',
                        'El monto agregado: '.($entity->getMontoTotalCalculado() - $montoAnterior).' no debe superar al Saldo: '.$this->devolverSaldo($entity->getCuposhatipoliquidacion()->getCupos()->getId())
                    );
                    return $this->redirect($this->generateUrl($direccion,array('id'=>$id)));
                }
                break;

            default:
                break;
        }



        $entity->setIdPersonalCargo($personalcargo);
        $entity->setRefCupoTipoLiquidacion($resCUHA);



        //die(var_dump($refRGpersonalcargo));
        if ($editForm->isValid()) {

            $em->persist($entity);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'aviso_ok',
                'Los datos fueron guardados correctamente!'
            );

            switch ($modo) {
            case 'rg':
                if ($idCuenta != 24) {
                    $direccion = "liquidaciones_nomina";
                    return $this->redirect($this->generateUrl($direccion, array('refCupo' => $entity->getCuposhatipoliquidacion()->getId(),'pagina'=>1)));
                } else {
                    $direccion = "liquidaciones_editdeuda";
                }
                return $this->redirect($this->generateUrl($direccion, array('id' => $id,'refRGpersonalcargo'=>$refRGpersonalcargo)));
                break;
            case 'horas':
                if ($idCuenta != 26) {
                    return $this->redirect($this->generateUrl('liquidaciones_ediths', array('id' => $id,'refRGpersonalcargo'=>0)));
                } else {
                    return $this->redirect($this->generateUrl('liquidaciones_edithsinvestigacion', array('id' => $id)));
                }
                break;
            case 'monto':
                return $this->redirect($this->generateUrl('liquidaciones_editmonto', array('id' => $id,'refRGpersonalcargo'=>0)));
                break;
            }

        }

        return array(
            'entity' => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'resultados' => $rResult,
            'resultadosReemplazo' => $rResultReemp,
            'cupo' => $entity->getCuposhatipoliquidacion()->getCupos()->getId(),
            'cupototal'=>$entity->getCuposhatipoliquidacion()->getCupos()->getMonto(),
            'cupogastado'=>$totalGastadoCupoMensual,
            'rg12' => $totalRG12,
            'rg24' => $totalRG24,
            'rg12f'=> $totalRG12F,
            'rg24f'=> $totalRG24F,
        );
    }
    /**
     * Deletes a Liquidaciones entity.
     *
     * @Route("liquidaciones_delete/{id}/{idcupo}", name="liquidaciones_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, $id, $idcupo)
    {
        //$form = $this->createDeleteForm($id,$idcupo);
        //$form->handleRequest($request);
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_2')) || (true === $securityContext->isGranted('ROLE_9')) ||
            (true === $securityContext->isGranted('ROLE_10')) || (true === $securityContext->isGranted('ROLE_11')) ||
            (true === $securityContext->isGranted('ROLE_12')) || (true === $securityContext->isGranted('ROLE_13')) ||
            (true === $securityContext->isGranted('ROLE_14')) || (true === $securityContext->isGranted('ROLE_16')) ||
            (true === $securityContext->isGranted('ROLE_20')) || (true === $securityContext->isGranted('ROLE_17')) || (true === $securityContext->isGranted('ROLE_22'))
                ){
            $em = $this->getDoctrine()->getManager("ms_haberes_web");
            $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Liquidaciones')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Liquidaciones entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get("session")->getFlashBag()->add(
                'aviso_ok',
                'El monto del agente fue eliminado correctamente!'
            );

            return $this->redirect($this->generateUrl('liquidaciones_nomina',array('refCupo' => $idcupo)));
        } else {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('liquidaciones_cuposliquidacion'));
        }
    }

    /**
     * Creates a form to delete a Liquidaciones entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id,$idcupo)
    {

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('liquidaciones_delete', array('id' => $id,'idcupo'=>$idcupo)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete','attr'   =>  array('class'   => 'form-control')))
            ->getForm()
        ;
    }


    /**
     *
     * @Route("/autorizarCarga/{id}/{refCupo}", name="liquidaciones_autorizarCarga")
     */
    public function autorizarCargaAction($id,$refCupo)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $entity = new Liquidaciones();

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Liquidaciones')->find($id);

        $entity->setRequiereAutorizacion('0');
        $entity->setUsuaAutoriza($this->getUser()->getUsername());
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('liquidaciones_autorizaciones',array('refCupo' => $refCupo)));
    }

    /**
     *
     * @Route("/exportarExcel/{refCupo}", name="liquidaciones_exportarExcel")
     */
    public function exportarExcelAction($refCupo, Request $request)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $entity = new Liquidaciones();

        $cupoHA = new \Liquidaciones\CuposAnualesBundle\Entity\CuposHATiposLiquidacion();

        $TotalGralLiquidado = $em->createQuery("SELECT sum(l.montoTotalCalculado)
                                FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
        JOIN l.cuposhatipoliquidacion ca
        WHERE ca.id= :idCupo ")->setParameter('idCupo', $refCupo)->getSingleScalarResult();

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);
        $cupoHA = $entities;

        $lstAgentes[] = null;


        //$contacts = new Liquidaciones();
        //$contacts = $em->getRepository('LiquidacionesCuposAnualesBundle:Liquidaciones')->findAll(); // Get All Contacts From DB
        $response = new Response();
        $excel = new \PHPExcel();

        $excel->getProperties()
            ->setCreator("Admin")
            ->setTitle("Mesajlar - ".date('d.m.Y'))
            ->setSubject("Mesajlar - ".date('d.m.Y'));

        $index = 2;

        $cuenta = $entities->getCuentas()->getModoCarga();
        $HAcupo = $entities->getId();
        $nombrecuenta = $entities->getCuentas()->getCuenta();
        $idtipoliquidacion = $entities->getCuentas()->getIdTipoLiquidacion();
        $anio = $entities->getCupos()->getAnio();
        $mes = $entities->getCupos()->getMes();
        $adicional = $entities->getAdicional();


        $exportFileName = "Nomina_Agentes_".$nombrecuenta."_".date("d_m_Y_H_i_s");

        $session = $this->getRequest()->getSession();
        $session->set('hatlcupo',$HAcupo);
        $session->set('cupo', $refCupo);
        $session->set('tipobusqueda', 'agente');
        $session->set('modocarga', $cuenta);

        switch ($cuenta) {
            case 'rg':


                $sqlAgentes = "SELECT   l.montoTotalCalculado as total,
                                    co.Nombre as concepto,
                                    l.idPersonalCargo as idpersonalcargo,
                                    pp.legajo,
                                    pp.apellido + ' ' + pp.nombre as apyn,
                                    l.rGFecha as fecha,
                                    ppr.apellido + ' ' + ppr.nombre as reemplazado,
                                    l.rGCantHsGuardia as cantidad,
                                    '0' as requireautorizacion,
                                    l.requiereAutorizacion,
                                    l.usuaAutoriza,
                                    l.id,
                                    l.usuacrea,
                                    l.fechacrea,
                                    d.IdDependencia,
                                    d.Dependencia
                             FROM LiquidacionesWeb.dbo.liquidaciones l
                  INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                  INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                  INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                  INNER JOIN Haberes.Haberes.HAConceptos co ON co.IdConcepto = l.idConcepto
                  INNER JOIN Haberes.General.HADependencias d on pc.iddependenciaparte = d.iddependencia
                  LEFT JOIN Haberes.Personal.HAPersonalCargos pcR ON pcR.idPersonalCargo = l.rGIdPersonalCargo
                  LEFT JOIN Haberes.Personal.HAPersonal ppR ON ppR.IdPersona = pcR.IdPersona
                  WHERE ca.id= ".$refCupo." order by l.fechacrea,Dependencia, pp.apellido, pp.nombre";


                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                $stmt = $conn->prepare($sqlAgentes);
                $stmt->execute();
                $rResult = 0;

                $rResult = $stmt->fetchAll();
                //var_dump($agentes);
                //var_dump($lstAgentes);

                $columns = array(
                        new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                        new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'filter' => 'select', 'selectFrom' => 'query','operatorsVisible'=> false, 'title' => 'Apellido y Nombre','sortable' => false)),
                        new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'operatorsVisible'=> false, 'title' => 'Legajo')),
                        new Column\TextColumn(array('id' => 'concepto', 'field' => 'concepto','filterable' => false, 'source' => true, 'title' => 'Guardia')),
                        new Column\TextColumn(array('id' => 'reemplazado', 'field' => 'reemplazado','filterable' => false, 'source' => true, 'title' => 'Reemplazado')),
                        new Column\DateColumn(array('id' => 'fecha', 'field' => 'fecha', 'source' => true,'filterable' => false, 'title' => 'Fecha')),
                        new Column\NumberColumn(array('id' => 'cantidad', 'field' => 'cantidad', 'source' => true,'filterable' => false, 'title' => 'Cantidad')),
                        new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                        new Column\BooleanColumn(array('id'=>'requireautorizacion','field'=>'requireautorizacion','filterable' => false, 'source'=>true, 'title' => 'Require Aut.')),
                        new Column\TextColumn(array('id' => 'usuarioautoriza', 'field' => 'usuarioautoriza', 'source' => true,'filterable' => false, 'title' => 'Usua. Aut.')),
                        new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                    );

                $excel->setActiveSheetIndex(0)->setCellValue("A1", "Cod.");
                $excel->setActiveSheetIndex(0)->setCellValue("B1", "Dependencia");
                $excel->setActiveSheetIndex(0)->setCellValue("C1", "Apellido y Nombre");
                $excel->setActiveSheetIndex(0)->setCellValue("D1", "Legajo");
                $excel->setActiveSheetIndex(0)->setCellValue("E1", "Guardia");
                $excel->setActiveSheetIndex(0)->setCellValue("F1", "Reemplazado");
                $excel->setActiveSheetIndex(0)->setCellValue("G1", "Fecha");
                $excel->setActiveSheetIndex(0)->setCellValue("H1", "Cantidad");
                $excel->setActiveSheetIndex(0)->setCellValue("I1", "Importe");
                $excel->setActiveSheetIndex(0)->setCellValue("J1", "UsuaCrea");
                $excel->setActiveSheetIndex(0)->setCellValue("K1", "Fecha Crea");

                // Add some data
                foreach($rResult as $agente)
                {
                    //die(var_dump($agente));
                    $excel->setActiveSheetIndex(0)->setCellValue("A".$index, $agente['IdDependencia']);
                    $excel->setActiveSheetIndex(0)->setCellValue("B".$index, utf8_encode($agente['Dependencia']));
                    $excel->setActiveSheetIndex(0)->setCellValue("C".$index, utf8_encode($agente['apyn']));
                    $excel->setActiveSheetIndex(0)->setCellValue("D".$index, $agente['legajo']);
                    $excel->setActiveSheetIndex(0)->setCellValue("E".$index, $agente['concepto']);
                    $excel->setActiveSheetIndex(0)->setCellValue("F".$index, $agente['reemplazado']);
                    $excel->setActiveSheetIndex(0)->setCellValue("G".$index, $agente['fecha']);
                    $excel->setActiveSheetIndex(0)->setCellValue("H".$index, $agente['cantidad']);
                    $excel->setActiveSheetIndex(0)->setCellValue("I".$index, $agente['total']);
                    $excel->setActiveSheetIndex(0)->setCellValue("J".$index, utf8_encode($agente['usuacrea']));
                    $excel->setActiveSheetIndex(0)->setCellValue("K".$index, $agente['fechacrea']);
                    //$createdAt = $contact->getCreatedAt();
                    //$excel->setActiveSheetIndex(0)->setCellValue("G".$index, $createdAt->format('Y-m-d H:i'));

                    $index++;
                }


                break;
            case 'horas':
                $sqlAgentes = "SELECT   l.montoTotalCalculado as total,
                                    co.Nombre as concepto,
                                    l.idPersonalCargo as idpersonalcargo,
                                    pp.legajo,
                                    pp.apellido + ' ' + pp.nombre as apyn,
                                    l.hsExValorHora as valorhora,
                                    l.hsExCantSimples as simples,
                                    l.hsExCantDobles as dobles,
                                    l.id
                             FROM LiquidacionesWeb.dbo.liquidaciones l
                  INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                  INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                  INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                  INNER JOIN Haberes.Haberes.HAConceptos co ON co.IdConcepto = l.idConcepto
                  WHERE ca.id= ".$refCupo;

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                $stmt = $conn->prepare($sqlAgentes);
                $stmt->execute();
                $rResult = 0;

                $rResult = $stmt->fetchAll();

                $columns = array(
                    new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                    new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'operatorsVisible'=> false,'filter' => 'select', 'selectFrom' => 'query', 'title' => 'Apellido y Nombre')),
                    new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'operatorsVisible'=> false, 'title' => 'Legajo')),
                    new Column\NumberColumn(array('id' => 'valorhora', 'field' => 'valorhora', 'source' => true,'filterable' => false, 'title' => 'Valor Hora')),
                    new Column\NumberColumn(array('id' => 'simples', 'field' => 'simples', 'source' => true,'filterable' => false, 'title' => 'Hs Simples')),
                    new Column\NumberColumn(array('id' => 'dobles', 'field' => 'dobles', 'source' => true,'filterable' => false, 'title' => 'Hs Dobles')),
                    new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                    new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                );

                $excel->setActiveSheetIndex(0)->setCellValue("A1", "Apellido y Nombre");
                $excel->setActiveSheetIndex(0)->setCellValue("B1", "Legajo");
                $excel->setActiveSheetIndex(0)->setCellValue("C1", "ValorHora");
                $excel->setActiveSheetIndex(0)->setCellValue("D1", "HsSimples");
                $excel->setActiveSheetIndex(0)->setCellValue("E1", "HsDobles");
                $excel->setActiveSheetIndex(0)->setCellValue("F1", "Importe");


                if ($rResult != null) {
                // Add some data
                    foreach($rResult as $agente)
                    {
                        $excel->setActiveSheetIndex(0)->setCellValue("A".$index, utf8_encode($agente['apyn']));
                        $excel->setActiveSheetIndex(0)->setCellValue("B".$index, $agente['legajo']);
                        $excel->setActiveSheetIndex(0)->setCellValue("C".$index, $agente['valorhora']);
                        $excel->setActiveSheetIndex(0)->setCellValue("D".$index, $agente['simples']);
                        $excel->setActiveSheetIndex(0)->setCellValue("E".$index, $agente['dobles']);
                        $excel->setActiveSheetIndex(0)->setCellValue("F".$index, $agente['total']);
                        //$createdAt = $contact->getCreatedAt();
                        //$excel->setActiveSheetIndex(0)->setCellValue("G".$index, $createdAt->format('Y-m-d H:i'));

                        $index++;
                    }
                }

                break;
            default:
                $sqlAgentes = "SELECT   l.montoTotalCalculado as total,
                                    co.Nombre as concepto,
                                    l.idPersonalCargo as idpersonalcargo,
                                    pp.legajo,
                                    pp.apellido + ' ' + pp.nombre as apyn,
                                    l.id
                             FROM LiquidacionesWeb.dbo.liquidaciones l
                  INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                  INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                  INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                  INNER JOIN Haberes.Haberes.HAConceptos co ON co.IdConcepto = l.idConcepto
                  WHERE ca.id= ".$refCupo;

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                $stmt = $conn->prepare($sqlAgentes);
                $stmt->execute();
                $rResult = 0;

                $rResult = $stmt->fetchAll();

                $columns = array(
                    new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                    new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true, 'title' => 'Apellido y Nombre')),
                    new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true, 'title' => 'Legajo')),
                    new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                    new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                );

                $excel->setActiveSheetIndex(0)->setCellValue("A1", "Apellido y Nombre");
                $excel->setActiveSheetIndex(0)->setCellValue("B1", "Legajo");
                $excel->setActiveSheetIndex(0)->setCellValue("C1", "Importe");

                // Add some data
                foreach($rResult as $agente)
                {
                    $excel->setActiveSheetIndex(0)->setCellValue("A".$index, utf8_encode($agente['apyn']));
                    $excel->setActiveSheetIndex(0)->setCellValue("B".$index, $agente['legajo']);
                    $excel->setActiveSheetIndex(0)->setCellValue("C".$index, $agente['total']);

                    //$createdAt = $contact->getCreatedAt();
                    //$excel->setActiveSheetIndex(0)->setCellValue("G".$index, $createdAt->format('Y-m-d H:i'));

                    $index++;
                }

                break;
        }






        // Set active sheet index to the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel5)
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$exportFileName.'.xls"');
        $response->headers->set('Cache-Control', 'max-age=0');
        $response->prepare($request);
        $response->sendHeaders();
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');
        exit();
    }

    /**
     *
     * @Route("/exportarExcel2/{refCupo}", name="liquidaciones_exportarExcel2")
     */
    public function exportarExcel2Action($refCupo)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $entity = new Liquidaciones();

        $TotalGralLiquidado = $em->createQuery("SELECT sum(l.montoTotalCalculado)
                                FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
        JOIN l.cuposhatipoliquidacion ca
        WHERE ca.id= :idCupo ")->setParameter('idCupo', $refCupo)->getSingleScalarResult();

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findBy(array("refCupo"=>$refCupo));

        foreach ($entities as $cupo) {
            $cuenta = $cupo->getCuentas()->getModoCarga();
            $HAcupo = $cupo->getId();
            $nombrecuenta = $cupo->getCuentas()->getCuenta();
            $idtipoliquidacion = $cupo->getCuentas()->getIdTipoLiquidacion();
            $anio = $cupo->getCupos()->getAnio();
            $mes = $cupo->getCupos()->getMes();
            $adicional = $cupo->getAdicional();
        }


        $session = $this->getRequest()->getSession();
        $session->set('hatlcupo',$HAcupo);
        $session->set('cupo', $refCupo);
        $session->set('tipobusqueda', 'agente');
        $session->set('modocarga', $cuenta);

        switch ($cuenta) {
            case 'rg':


                $sqlAgentes = "SELECT   l.montoTotalCalculado as total,
                                    co.Nombre as concepto,
                                    l.idPersonalCargo as idpersonalcargo,
                                    pp.legajo,
                                    pp.apellido + ' ' + pp.nombre as apyn,
                                    l.rGFecha as fecha,
                                    ppr.apellido + ' ' + ppr.nombre as reemplazado,
                                    l.rGCantHsGuardia as cantidad,
                                    0 as requireautorizacion,
                                    l.requiereAutorizacion,
                                    l.usuaAutoriza,
                                    l.id
                             FROM LiquidacionesWeb.dbo.liquidaciones l
                  INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                  INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                  INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                  INNER JOIN Haberes.Haberes.HAConceptos co ON co.IdConcepto = l.idConcepto
                  LEFT JOIN Haberes.Personal.HAPersonalCargos pcR ON pcR.idPersonalCargo = l.rGIdPersonalCargo
                  LEFT JOIN Haberes.Personal.HAPersonal ppR ON ppR.IdPersona = pcR.IdPersona
                  WHERE ca.id= ".$refCupo;


                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                $stmt = $conn->prepare($sqlAgentes);
                $stmt->execute();
                $rResult = 0;

                $rResult = $stmt->fetchAll();
                //var_dump($agentes);
                //var_dump($lstAgentes);

                $columns = array(
                        new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                        new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'filter' => 'select', 'selectFrom' => 'query','operatorsVisible'=> false, 'title' => 'Apellido y Nombre','sortable' => false)),
                        new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'operatorsVisible'=> false, 'title' => 'Legajo')),
                        new Column\TextColumn(array('id' => 'concepto', 'field' => 'concepto','filterable' => false, 'source' => true, 'title' => 'Guardia')),
                        new Column\TextColumn(array('id' => 'reemplazado', 'field' => 'reemplazado','filterable' => false, 'source' => true, 'title' => 'Reemplazado')),
                        new Column\DateColumn(array('id' => 'fecha', 'field' => 'fecha', 'source' => true,'filterable' => false, 'title' => 'Fecha')),
                        new Column\NumberColumn(array('id' => 'cantidad', 'field' => 'cantidad', 'source' => true,'filterable' => false, 'title' => 'Cantidad')),
                        new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                        new Column\BooleanColumn(array('id'=>'requireautorizacion','field'=>'requireautorizacion','filterable' => false, 'source'=>true, 'title' => 'Require Aut.')),
                        new Column\TextColumn(array('id' => 'usuarioautoriza', 'field' => 'usuarioautoriza', 'source' => true,'filterable' => false, 'title' => 'Usua. Aut.')),
                        new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                    );
                break;
            case 'horas':
                $sqlAgentes = "SELECT   l.montoTotalCalculado as total,
                                    co.Nombre as concepto,
                                    l.idPersonalCargo as idpersonalcargo,
                                    pp.legajo,
                                    pp.apellido + ' ' + pp.nombre as apyn,
                                    l.hsExValorHora as valorhora,
                                    l.hsExCantSimples as simples,
                                    l.hsExCantDobles as dobles,
                                    l.id
                             FROM LiquidacionesWeb.dbo.liquidaciones l
                  INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                  INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                  INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                  INNER JOIN Haberes.Haberes.HAConceptos co ON co.IdConcepto = l.idConcepto
                  WHERE ca.id= ".$refCupo;

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                $stmt = $conn->prepare($sqlAgentes);
                $stmt->execute();
                $rResult = 0;

                $rResult = $stmt->fetchAll();

                $columns = array(
                    new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                    new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'operatorsVisible'=> false,'filter' => 'select', 'selectFrom' => 'query', 'title' => 'Apellido y Nombre')),
                    new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'operatorsVisible'=> false, 'title' => 'Legajo')),
                    new Column\NumberColumn(array('id' => 'valorhora', 'field' => 'valorhora', 'source' => true,'filterable' => false, 'title' => 'Valor Hora')),
                    new Column\NumberColumn(array('id' => 'simples', 'field' => 'simples', 'source' => true,'filterable' => false, 'title' => 'Hs Simples')),
                    new Column\NumberColumn(array('id' => 'dobles', 'field' => 'dobles', 'source' => true,'filterable' => false, 'title' => 'Hs Dobles')),
                    new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                    new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                );
                break;
            default:
                $sqlAgentes = "SELECT   l.montoTotalCalculado as total,
                                    co.Nombre as concepto,
                                    l.idPersonalCargo as idpersonalcargo,
                                    pp.legajo,
                                    pp.apellido + ' ' + pp.nombre as apyn,
                                    l.id
                             FROM LiquidacionesWeb.dbo.liquidaciones l
                  INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                  INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                  INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                  INNER JOIN Haberes.Haberes.HAConceptos co ON co.IdConcepto = l.idConcepto
                  WHERE ca.id= ".$refCupo;

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                $stmt = $conn->prepare($sqlAgentes);
                $stmt->execute();
                $rResult = 0;

                $rResult = $stmt->fetchAll();

                $columns = array(
                    new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                    new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true, 'title' => 'Apellido y Nombre')),
                    new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true, 'title' => 'Legajo')),
                    new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                    new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                );

                break;
        }

        if (isset($rResult)) {
            //$lstAgentes



        } else {
            $lstAgentes = array(array('Apellido y Nombre' => null,
                                    'Importe' => null));


        }

        return $grid->getGridResponse('LiquidacionesCuposAnualesBundle:Liquidaciones:exportarExcel.html.twig',
                array('grid'=>$grid,'refcupo'=>$refCupo));
    }



    /**
     *
     * @Route("/exportarExcelValorHora/", name="liquidaciones_exportarExcelValorHora")
     */
    public function exportarExcelValorHoraAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        $sql     = "select
                        top 1
                        l.anio,l.mes
                    from        Haberes.Liquidacion.HALiquidacion l
                    where       l.IdTipoLiquidacion = 37
                    order by    1 desc,2 desc";

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $response = new Response();

        $rResult = '';

        $rResult = $stmt->fetchAll();

        $anio = $rResult[0]['anio'];
        $mes  = substr('0'.$rResult[0]['mes'],-2);

        $sql = "select p.legajo as Legajo, p.Apellido, p.Nombre,p.NroDocumento,
                convert(decimal(18,2),haberes.Haberes.fValorHoraLiquidado
                ('".$mes."/01/".$anio."',refpersonalcargo)) as ValorHora, convert(varchar,dep.IdDependencia) + ' - ' + dep.Dependencia as depedencia

                from haberes.Copia.HAPersonal p
                inner join haberes.haberes.HAAgrupamiento a
                on p.IdAgrupamiento=a.IdAgrupamiento
                inner join haberes.haberes.HAPlanta pl
                on pl.IdPlanta=p.IdPlanta
                inner join haberes.General.HADependencias dep
                on  dep.IdDependencia = p.IdDependenciaParte
                where p.RefCabeceraCopia=4 -- ultima copia
                and p.IdRegimenEstatutario=1
                and p.FechaBajaPreventiva is null
                and p.IdDependenciaPlantel = ".$usuarioDependencia."
                group by p.legajo,p.NroDocumento, p.Apellido, p.Nombre, pl.Planta,
                p.RefPersonalCargo, a.Descripcion,dep.IdDependencia, dep.Dependencia
                order by Apellido, nombre";



        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $rResult = '';

        $rResult = $stmt->fetchAll();


        $excel = new \PHPExcel();

        $excel->getProperties()
            ->setCreator("Admin")
            ->setTitle("Mesajlar - ".date('d.m.Y'))
            ->setSubject("Mesajlar - ".date('d.m.Y'));

        $index = 2;


        $exportFileName = "Nomina_Agentes_valor_hora_".date("d_m_Y");

        $excel->setActiveSheetIndex(0)->setCellValue("A1", "Legajo");
        $excel->setActiveSheetIndex(0)->setCellValue("B1", "Apellido");
        $excel->setActiveSheetIndex(0)->setCellValue("C1", "Nombre");
        $excel->setActiveSheetIndex(0)->setCellValue("D1", "NroDocumento");
        $excel->setActiveSheetIndex(0)->setCellValue("E1", "Valor Hora");

        // Add some data
        foreach($rResult as $agente)
        {
            //die(var_dump($agente));
            $excel->setActiveSheetIndex(0)->setCellValue("A".$index, $agente['Legajo']);
            $excel->setActiveSheetIndex(0)->setCellValue("B".$index, utf8_encode($agente['Apellido']));
            $excel->setActiveSheetIndex(0)->setCellValue("C".$index, utf8_encode($agente['Nombre']));
            $excel->setActiveSheetIndex(0)->setCellValue("D".$index, $agente['NroDocumento']);
            $excel->setActiveSheetIndex(0)->setCellValue("E".$index, $agente['ValorHora']);
            //$createdAt = $contact->getCreatedAt();
            //$excel->setActiveSheetIndex(0)->setCellValue("G".$index, $createdAt->format('Y-m-d H:i'));

            $index++;
        }


        // Set active sheet index to the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel5)
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$exportFileName.'.xls"');
        $response->headers->set('Cache-Control', 'max-age=0');
        $response->prepare($request);
        $response->sendHeaders();
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');
        exit();



        /*
        return $grid->getGridResponse('LiquidacionesCuposAnualesBundle:Liquidaciones:exportarExcel.html.twig',
                array('grid'=>$grid,'refcupo'=>$refCupo));
                */
    }


    public function devolverSaldo($idCupo) {

        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $entity = new \Liquidaciones\CuposAnualesBundle\Entity\Cupos();

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->find($idCupo);

        $Fecha = $em->createQuery('SELECT c.anio, c.mes FROM LiquidacionesCuposAnualesBundle:Cupos c
                  WHERE c.id = :idCupo
                  GROUP BY c.anio, c.mes')->setParameter('idCupo', $idCupo)->getResult();
        $anio = $Fecha[0]["anio"];
        $mes = $Fecha[0]["mes"];

        $total = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                  JOIN l.cuposhatipoliquidacion ca
                  WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $idCupo)->getResult();

        if ($total != null) {
            $totalGastadoCupoMensual = $total[0]["total"] == 0 ? 0 : $total[0]["total"];
        } else {
            $totalGastadoCupoMensual = 0;
        }

        return $entity->getMonto() - $totalGastadoCupoMensual;

    }


    public function cupoCerradoPorId($idCupo) {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $entity = new \Liquidaciones\CuposAnualesBundle\Entity\Cupos();

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($idCupo);

        if ($entity->getCupos()->getCupoestado()->getCupoEstado() == "Abierto") {
            return false;
        } else {
            return true;
        }
    }

    public function permiteCargaAgente($idpersonalcargo,$modocarga,$idtipoliquidacion, $idCuenta,$anio,$mes) {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $TipoBusqueda = $this->getRequest()->getSession()->get('tipobusqueda');

        $valor = 0;
        switch ($idCuenta) {
            case 22:
                $valor = 2;
                break;
            case 21:
                $valor = 3;
                break;
            case 23:
                $valor = 4;
				break;
            case 7:
                $valor = 5;
                break;
            case 25:
                $valor = 4;
                break;
            default:
                $valor = 3;
                break;
        }

        if ($TipoBusqueda != 'agente') {
        	$valor = 1; //persona a la que voy a reemplazar
        }

        $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$idpersonalcargo.',4, null, '.$idtipoliquidacion.', '.$anio.', '.$mes.','.$valor;
        //die(var_dump($sql));

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $rResult = 0;

        $rResult = $stmt->fetchAll();

        //die(var_dump($rResult[0]));
        if (substr( $rResult[0]["validacion"],0,1) == 1) {
            return true;
        } else {
            return false;
        }

    }


    function array_orderby()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
                }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

    //return $this->buildParte($parte,$anexo,$tipoParte,$dependencia,$parte_descrip,$anio,$mes);




    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/valorhora/", name="liquidaciones_valorhora")
     * @Method("GET|POST")
     * @Template()
     */
    public function valorhoraAction() {

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');


        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        $depe = $em->getRepository('LiquidacionesHaberesBundle:HADependencias')->findOneBy(array('id' => $usuarioDependencia));

        return array(
            'depe' => $depe,
        );
}



    /**
     * Displays a form to create a new Liquidaciones entity.
     *
     * @Route("/finalizarestado/{refcupo}", name="liquidaciones_finalizarestado")
     */
    public function finalizarestadoAction($refcupo, Request $request) {

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refcupo);
        //die(var_dump($cuposHATL));
        $cupos      =  $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->find($cuposHATL->getCupos()->getId());

        $cupos->setRefCupoEstado(5);
        $em->persist($cupos);
        $em->flush();

        $request->getSession()->getFlashBag()->add(
            'aviso_ok',
            'Se Finalizo la carga del Cupo!'
        );
        // ... adicionalmente modifica la respuesta o la devuelve
        //     directamente
        return $this->redirect($this->generateUrl('liquidaciones_nomina',array('refCupo' => $refcupo)));
    }


    /**
     *
     * @Route("/exportarPDF/{refCupo}", name="liquidaciones_exportarPDF")
     */
    public function exportarPDFAction($refCupo, Request $request)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);


        $esPresupuestaria = $entities->getCuentas()->getEsPresupuestaria();
        $HAcupo = $entities->getId();
        $cuenta = $entities->getCuentas()->getCuenta();
        $modocarga = $entities->getCuentas()->getModoCarga();
        $idtipoliquidacion = $entities->getCuentas()->getIdTipoLiquidacion();
        $anio = $entities->getCupos()->getAnio();
        $mes = $entities->getCupos()->getMes();
        $adicional = $entities->getAdicional();
        $depe = $em->getRepository('LiquidacionesHaberesBundle:HADependencias')->findOneBy(array('id' => $entities->getCupos()->getIdDependencia()));
        //$dependencia = $emR->getRepository('LiquidacionesReferenciasBundle:Dependencias')->find($cupo->getIdDependencia());
        //$depe = $dependencia->getId();

        //die(var_dump($modocarga));

        if ($esPresupuestaria == 1) {
            switch ($modocarga) {
                case 'horas':
                    $datos = $this->devolverNominaHs($refCupo);
                    break;
                case 'rg':
                    $datos = $this->devolverNominaRG($refCupo);
                    break;
                case 'monto':
                    $datos = $this->devolverNominaRG($refCupo);
                    break;
            }
        } else {
            $datos = $this->devolverNominaHs($refCupo);
        }


        //die(var_dump($depe));
        return $this->buildReporte($datos,((integer)$depe->getId()).' - '.$depe->getDependencia(),$modocarga,$esPresupuestaria,$anio,$mes,$cuenta);

    }





    public function buildReporte($datos,$depe,$modocarga,$esPresupuestaria,$anio,$mes,$cuenta) {

        $pdf = $this->get("white_october.tcpdf")->create();

        //set default header data
        $PDF_HEADER_LOGO = "logo-recibo.jpg";//any image file. check correct path.
        $PDF_HEADER_LOGO_WIDTH = "40";

        $direccionHoja = 'P';

        if ($esPresupuestaria == 3) {
            $PDF_HEADER_TITLE = 'Listado de respaldo de Interdepósitos:';
            $direccionHoja = 'P';
        } else {
            switch ($modocarga) {
                case 'horas':
                    $PDF_HEADER_TITLE = '49-Horas Extras';
                    $direccionHoja = 'P';
                    break;
                case 'rg':
                    if ($cuenta == 'Reemplazos de Guardias - Pago Deuda') {
                        $PDF_HEADER_TITLE = '11-Deuda - Reemplazo de Guardias';
                    } else {
                        $PDF_HEADER_TITLE = '11-Reemplazo de Guardias';
                    }

                    $direccionHoja = 'L';
                    break;
                case 'monto':
                    $PDF_HEADER_TITLE = 'Liquidaciones Por Montos';
                    $direccionHoja = 'P';
                    break;
            }
        }


        $PDF_HEADER_STRING = "Ministerio de Salud - ".$cuenta." - ".$anio."-".$mes."\n";

        $PDF_HEADER_STRING .= $depe;
        $pdf->SetHeaderData($PDF_HEADER_LOGO, $PDF_HEADER_LOGO_WIDTH, $PDF_HEADER_TITLE, $PDF_HEADER_STRING);
        //$pdf->setFooterData(array(0,0,0), array(0,0,0));
        $pdf->setFooterData(array(0,64,0), array(0,64,128));


        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Ministerio de Salud de la Provincia de Buenos Aires');
        $pdf->SetTitle('Listado de respaldo de Interdepósitos:');
        $pdf->SetSubject('Parte de novedades');

        // set margins
        // $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetMargins(10, 46, 10);

        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // set border width
        $pdf->SetLineWidth(0.2);

        // set color for cell border
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetFillColor(224,224,224);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage($direccionHoja,'A4', FALSE, FALSE);
        $cant = 0;
        $pdf->SetFont('helvetica', '', 10);


        $totalGeneral = 0;

        if ($esPresupuestaria == 0) {
            foreach ($datos as $agente) {
                /*
                $cant += 1;
                $pdf->Cell(15, 0, $agente['sucursal'], 'LTB', 0, 'L', 0, '', 1);
                $pdf->Cell(17, 0, $agente['cuenta'], 'LTB', 0, 'C', 0, '', 1);
                $pdf->Cell(8, 0, $agente['dv'], 'LTB', 0, 'C', 0, '', 1);
                $pdf->Cell(16, 0, '0096', 'LTB', 0, 'C', 0, '', 1);
                $pdf->Cell(25, 0, "$ ".number_format($agente['total'],2,',','.'), 'LTB', 0, 'R', 0, '', 0);
                $pdf->Cell(8, 0, '', 'LTB', 0, 'C', 0, '', 1);
                $pdf->Cell(105, 0, utf8_encode($agente['apyn']), 'LTRB', 0, 'L', 0, '', 1);
                $totalGeneral = $totalGeneral + (float)$agente['total'];
                $pdf->Ln();
                */
                $cant += 1;
                $pdf->Cell(12, 0, $agente['legajo'], 'LTB', 0, 'C', 0, '', 1);
                $pdf->Cell(75, 0, utf8_encode($agente['apyn']), 'LTB', 0, 'L', 0, '', 1);
                $pdf->Cell(17, 0, $agente['valorhora'], 'LTB', 0, 'C', 0, '', 1);
                $pdf->Cell(17, 0, $agente['simples'], 'LTB', 0, 'C', 0, '', 1);
                $pdf->Cell(17, 0, $agente['dobles'], 'LTB', 0, 'C', 0, '', 0);
                $pdf->Cell(25, 0, "$ ".number_format($agente['total'],2,',','.'), 'LTRB', 0, 'R', 0, '', 0);
                $totalGeneral = $totalGeneral + (float)$agente['total'];

                $pdf->Ln();
            }

        } else {
            switch ($modocarga) {
                case 'horas':
                    foreach ($datos as $agente) {
                        $cant += 1;
                        $pdf->Cell(12, 0, $agente['legajo'], 'LTB', 0, 'C', 0, '', 1);
                        $pdf->Cell(75, 0, utf8_encode($agente['apyn']), 'LTB', 0, 'L', 0, '', 1);
                        $pdf->Cell(17, 0, $agente['valorhora'], 'LTB', 0, 'C', 0, '', 1);
                        $pdf->Cell(17, 0, $agente['simples'], 'LTB', 0, 'C', 0, '', 1);
                        $pdf->Cell(17, 0, $agente['dobles'], 'LTB', 0, 'C', 0, '', 0);
                        $pdf->Cell(25, 0, "$ ".number_format($agente['total'],2,',','.'), 'LTRB', 0, 'R', 0, '', 0);
                        $totalGeneral = $totalGeneral + (float)$agente['total'];

                        $pdf->Ln();
                    }
                    break;
                case 'rg':

                    if ($cuenta == 'Reemplazos de Guardias - Pago Deuda') {
                        foreach ($datos as $agente) {
                            $cant += 1;
                            $pdf->Cell(12, 0, $agente['legajo'], 'LTB', 0, 'C', 0, '', 1);
                            $pdf->Cell(70, 0, utf8_encode($agente['apyn']), 'LTB', 0, 'L', 0, '', 1);
                            $pdf->Cell(25, 0, $agente['concepto'], 'LTB', 0, 'C', 0, '', 1);
                            $pdf->Cell(17, 0, $agente['cantidad'], 'LTB', 0, 'C', 0, '', 1);
                            $pdf->Cell(70, 0, $agente['reemplazado'], 'LTB', 0, 'L', 0, '', 0);
                            $pdf->Cell(25, 0, "$ ".number_format($agente['total'],2,',','.'), 'LTRB', 0, 'R', 0, '', 0);
                            $totalGeneral = $totalGeneral + (float)$agente['total'];
                            $pdf->Ln();
                        }
                    } else {
                        foreach ($datos as $agente) {
                            $cant += 1;
                            $pdf->Cell(12, 0, $agente['legajo'], 'LTB', 0, 'C', 0, '', 1);
                            $pdf->Cell(70, 0, utf8_encode($agente['apyn']), 'LTB', 0, 'L', 0, '', 1);
                            $pdf->Cell(25, 0, $agente['concepto'], 'LTB', 0, 'C', 0, '', 1);
                            $pdf->Cell(17, 0, $agente['cantidad'], 'LTB', 0, 'C', 0, '', 1);
                            $pdf->Cell(70, 0, $agente['reemplazado'], 'LTB', 0, 'L', 0, '', 0);
                            $pdf->Cell(25, 0, ($agente['fechareemplazo'] == '' ? '' : $agente['fechareemplazo']), 'LTB', 0, 'C', 0, '', 0);
                            $pdf->Cell(25, 0, "$ ".number_format($agente['total'],2,',','.'), 'LTRB', 0, 'R', 0, '', 0);
                            $totalGeneral = $totalGeneral + (float)$agente['total'];
                            $pdf->Ln();
                        }
                    }


                    break;
                case 'monto':
                    foreach ($datos as $agente) {
                        $cant += 1;
                        $pdf->Cell(12, 0, $agente['legajo'], 'LTB', 0, 'C', 0, '', 1);
                        $pdf->Cell(75, 0, utf8_encode($agente['apyn']), 'LTB', 0, 'L', 0, '', 1);
                        $pdf->Cell(17, 0, $agente['cantidad'], 'LTB', 0, 'C', 0, '', 1);
                        $pdf->Cell(25, 0, "$ ".number_format($agente['total'],2,',','.'), 'LTRB', 0, 'R', 0, '', 0);
                        $totalGeneral = $totalGeneral + (float)$agente['total'];

                        $pdf->Ln();
                    }
                    break;
            }
        }


        // ---------------------------------------------------------

        // final del archivo, total agentes y total general //
        $pdf->Ln();
        $pdf->Cell(75, 0, 'Cantidad de Agentes: '.$cant, '', 0, 'L', 0, '', 1);
        $pdf->Ln();
        $pdf->Cell(75, 0, 'Total General: $'.number_format($totalGeneral,2,',','.'), '', 0, 'L', 0, '', 1);

        //                  fin                             //



        $response = new Response($pdf->Output('Listado_Respaldo.pdf',"D"));

        $response->headers->set('Content-Type', 'application/pdf');

        // Send headers before outputting anything
        $response->sendHeaders();

        return $response;
    }


    public function devolverNomina3ro($refCupo) {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $entity = new Liquidaciones();

        $TotalGralLiquidado = 0;
        $lstAgentes = array();
        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);

        $cuenta = $entities->getCuentas()->getModoCarga();
        $HAcupo = $entities->getId();
        $nombrecuenta = $entities->getCuentas()->getCuenta();
        $idtipoliquidacion = $entities->getCuentas()->getIdTipoLiquidacion();
        $anio = $entities->getCupos()->getAnio();
        $mes = $entities->getCupos()->getMes();
        $adicional = $entities->getAdicional();
        $idCupoAnual = $entities->getCupos()->getCuposanuales()->getId();



        $sql =       "select sum(l.montoTotalCalculado) as total,
                                l.idConcepto as concepto,
                                l.idPersonalCargo,
                                d.id as codigo,
                                SUM(l.rGCantHsGuardia) as cantidad "
                        ." FROM        LiquidacionesCuposAnualesBundle:Liquidaciones l "
                        ." INNER JOIN "
                        ." LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion ca "
                        ." WITH          ca.id = l.refCupoTipoLiquidacion "
                        ." INNER JOIN "
                        ." LiquidacionesCuposAnualesBundle:Cupos c "
                        ." WITH          c.id = ca.refCupo "
                        ." INNER JOIN "
                        ." LiquidacionesHaberesBundle:HADependencias d "
                        ." WITH          d.id = c.idDependencia "
                        ." INNER JOIN "
                        ." LiquidacionesCuposAnualesBundle:CuposAnuales a "
                        ." WITH          a.id = c.refCupoAnual "
                        ." INNER JOIN "
                        ." LiquidacionesCuposAnualesBundle:Cuentas cc "
                        ." WITH          cc.id = ca.refCuenta "
                        ." INNER JOIN "
                        ." LiquidacionesCuposAnualesBundle:CupoEstados e "
                        ." WITH          e.id = c.RefCupoEstado "
                        ." WHERE a.id = ".$idCupoAnual." AND c.mes = ".$mes." AND c.anio = ".$anio."  and ca.id =".$refCupo." "
                        ." GROUP BY l.idConcepto,l.idPersonalCargo,d.id ";

            $agentes = $em->createQuery($sql)->getResult();

        foreach ($agentes as $item) {
            //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
            $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$item['idPersonalCargo'].',5';

            $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $rResult = 0;

            $rResult = $stmt->fetchAll();

            $TotalGralLiquidado = $TotalGralLiquidado + $item['total'];

            $lstAgentesU = array('idpersonalcargo' => $item['idPersonalCargo'],
                            'apyn'=> $rResult[0]['apyn'],
                            'legajo'=> $rResult[0]['Legajo'],
                            'sucursal'=> $rResult[0]['Sucursal'],
                            'cuenta'=> $rResult[0]['Cuenta'],
                            'dv'=> $rResult[0]['dv'],
                            'valorhora'=>'',
                            'simples'=>'',
                            'dobles'=>'',
                            'concepto'=>$item['concepto'],
                            'reemplazado'=> '',
                            'fechareemplazo'=>'',
                            'dependencia'=> $item['codigo'],
                            'cantidad'=>'',
                            'total'=>$item['total']);
            $lstAgentes[] = $lstAgentesU;
        }

        return $lstAgentes;
    }



    public function devolverNominaHs($refCupo) {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $entity = new Liquidaciones();

        $TotalGralLiquidado = $em->createQuery("SELECT sum(l.montoTotalCalculado)
                                FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
        JOIN l.cuposhatipoliquidacion ca
        WHERE ca.id= :idCupo ")->setParameter('idCupo', $refCupo)->getSingleScalarResult();

        $lstAgentes = array();
        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);

        $anio = $entities->getCupos()->getAnio();
        $mes = $entities->getCupos()->getMes();
        $adicional = $entities->getAdicional();
        $idCupoAnual = $entities->getCupos()->getCuposAnuales()->getId();


        $sql = "select sum(l.montoTotalCalculado) as total,
                        l.idPersonalCargo as idpersonalcargo,
                        convert(varchar(6),d.iddependencia) + '-' + d.dependencia as dependencia,
                        sum(l.hsExValorHora) as valorhora,
                        sum(l.hsExCantSimples) as simples,
                        sum(l.hsExCantDobles) as dobles,
                        pp.apellido + ' ' + pp.nombre as apyn,
                        pp.legajo,
                        l.idConcepto,
                        '' as sucursal,
                        '' as cuenta,
                        '' as dv,
                        '' as reemplazado,
                        '' as fechareemplazo,
                        '' as cantidad
                FROM LiquidacionesWeb.dbo.liquidaciones l
                INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                INNER JOIN Haberes.Haberes.HAAgrupamiento ag ON ag.idagrupamiento = pc.IdAgrupamiento
                INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                INNER JOIN LiquidacionesWeb.dbo.cupos c on c.id = ca.refCupo
                INNER JOIN Haberes.General.HADependencias d on d.iddependencia = c.idDependencia
                INNER JOIN LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refCupoAnual
                INNER JOIN LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refCuenta "
                ." WHERE a.id = ".$idCupoAnual." AND c.mes = ".$mes." AND c.anio = ".$anio." AND ca.id = ".$refCupo." "
                ." GROUP BY l.idPersonalCargo, d.iddependencia,d.dependencia,l.idConcepto,pp.apellido ,pp.nombre ,pp.legajo"
                ." ORDER BY d.iddependencia, pp.apellido, pp.nombre";

        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $agentes = $connection ->  prepare($sql);
        $agentes -> execute();
        //die(var_dump($sql));

        return $agentes;
    }



    public function devolverNominaRG($refCupo) {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $entity = new Liquidaciones();

        $TotalGralLiquidado = $em->createQuery("SELECT sum(l.montoTotalCalculado)
                                FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
        JOIN l.cuposhatipoliquidacion ca
        WHERE ca.id= :idCupo ")->setParameter('idCupo', $refCupo)->getSingleScalarResult();

        $lstAgentes = array();
        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);


        $anio = $entities->getCupos()->getAnio();
        $mes = $entities->getCupos()->getMes();
        $adicional = $entities->getAdicional();
        $idCupoAnual = $entities->getCupos()->getCuposanuales()->getId();
        $idtipoliquidacion = $entities->getCuentas()->getIdTipoLiquidacion();
		$idCuenta = $entities->getCuentas()->getId();


        //TREFE14.Dependencias d on c.iddependencia = d.codigo
		if ($idCuenta == 24) {
        	$sql = "select sum(l.montoTotalCalculado) as total,
                        l.idPersonalCargo as idpersonalcargo,
                        convert(varchar(6),d.iddependencia) + '-' + d.dependencia as dependencia,
                        '' as valorhora,
                        '' as simples,
                        '' as dobles,
                        pp.apellido + ' ' + pp.nombre as apyn,
                        pp.legajo,
                        l.idConcepto,
                        '' as sucursal,
                        '' as cuenta,
                        '' as dv,
                        (case when l.rGIdPersonalCargo is not null then ppr.apellido + ' ' + ppr.nombre else '' end) as reemplazado,
                        l.rGFecha as fechareemplazo,
                        SUM(l.rGCantHsGuardia) as cantidad,
                        co.nombre as concepto
                FROM LiquidacionesWeb.dbo.liquidaciones l
                INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                LEFT JOIN Haberes.Personal.HAPersonalCargos pcr ON pcr.idPersonalCargo = l.rGIdPersonalCargo
                LEFT JOIN Haberes.Personal.HAPersonal ppr ON ppr.IdPersona = pcr.IdPersona
                INNER JOIN Haberes.Haberes.HAConceptos co ON co.idconcepto = l.idconcepto
                INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                INNER JOIN LiquidacionesWeb.dbo.cupos c on c.id = ca.refCupo
                INNER JOIN Haberes.General.HADependencias d on d.iddependencia = c.idDependencia
                INNER JOIN LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refCupoAnual
                INNER JOIN LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refCuenta "
                ." WHERE a.id = ".$idCupoAnual." AND ca.id =".$refCupo." "
                ." GROUP BY l.idPersonalCargo,ppr.apellido,ppr.nombre, d.iddependencia,ppr.legajo,l.rGIdPersonalCargo,d.dependencia,l.idConcepto,pp.apellido ,pp.nombre ,pp.legajo, l.rGFecha,co.nombre"
                ." ORDER BY d.iddependencia, pp.apellido, pp.nombre";
        } else {
			$sql = "select sum(l.montoTotalCalculado) as total,
                        l.idPersonalCargo as idpersonalcargo,
                        convert(varchar(6),d.iddependencia) + '-' + d.dependencia as dependencia,
                        '' as valorhora,
                        '' as simples,
                        '' as dobles,
                        pp.apellido + ' ' + pp.nombre as apyn,
                        pp.legajo,
                        l.idConcepto,
                        '' as sucursal,
                        '' as cuenta,
                        '' as dv,
                        (case when l.rGIdPersonalCargo is not null then ppr.apellido + ' ' + ppr.nombre else '' end) as reemplazado,
                        l.rGFecha as fechareemplazo,
                        SUM(l.rGCantHsGuardia) as cantidad,
                        co.nombre as concepto
                FROM LiquidacionesWeb.dbo.liquidaciones l
                INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                LEFT JOIN Haberes.Personal.HAPersonalCargos pcr ON pcr.idPersonalCargo = l.rGIdPersonalCargo
                LEFT JOIN Haberes.Personal.HAPersonal ppr ON ppr.IdPersona = pcr.IdPersona
                INNER JOIN Haberes.Haberes.HAConceptos co ON co.idconcepto = l.idconcepto
                INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                INNER JOIN LiquidacionesWeb.dbo.cupos c on c.id = ca.refCupo
                INNER JOIN Haberes.General.HADependencias d on d.iddependencia = c.idDependencia
                INNER JOIN LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refCupoAnual
                INNER JOIN LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refCuenta "
                ." WHERE a.id = ".$idCupoAnual." AND c.mes = ".$mes." AND c.anio = ".$anio." AND ca.id = ".$refCupo." "
                ." GROUP BY l.idPersonalCargo,ppr.apellido,ppr.nombre, d.iddependencia,ppr.legajo,l.rGIdPersonalCargo,d.dependencia,l.idConcepto,pp.apellido ,pp.nombre ,pp.legajo, l.rGFecha, co.nombre"
                ." ORDER BY d.iddependencia, pp.apellido, pp.nombre";
		}
        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $agentes = $connection ->  prepare($sql);
        $agentes -> execute();
        //die(var_dump($sql));

        return $agentes;

    }


    public function devolverNomina($refCupo) {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $entity = new Liquidaciones();

        $TotalGralLiquidado = $em->createQuery("SELECT sum(l.montoTotalCalculado)
                                FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
        JOIN l.cuposhatipoliquidacion ca
        WHERE ca.id= :idCupo ")->setParameter('idCupo', $refCupo)->getSingleScalarResult();

        $lstAgentes = array();
        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);


        $cuenta = $entities->getCuentas()->getModoCarga();
        $HAcupo = $entities->getId();
        $nombrecuenta = $entities->getCuentas()->getCuenta();
        $idtipoliquidacion = $entities->getCuentas()->getIdTipoLiquidacion();
        $anio = $entities->getCupos()->getAnio();
        $mes = $entities->getCupos()->getMes();
        $adicional = $entities->getAdicional();


        $session = $this->getRequest()->getSession();
        $session->set('hatlcupo',$HAcupo);
        $session->set('cupo', $refCupo);
        $session->set('tipobusqueda', 'agente');
        $session->set('modocarga', $cuenta);

        switch ($cuenta) {
            case 'rg':

                $sql =  "select sum(l.montoTotalCalculado) as total,
                                l.idPersonalCargo as idpersonalcargo,
                                convert(varchar(6),d.iddependencia) + '-' + d.dependencia as dependencia,
                                pp.apellido + ' ' + pp.nombre as apyn,
                                pp.legajo,
                                l.idConcepto,
                                (case when l.rGIdPersonalCargo is not null then ppr.apellido + ' ' + ppr.nombre else '' end) as reemplazado,
                                l.rGFecha as fechareemplazo,
                                SUM(l.rGCantHsGuardia) as cantidad
                        FROM LiquidacionesWeb.dbo.liquidaciones l
                        INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                        INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                        LEFT JOIN Haberes.Personal.HAPersonalCargos pcr ON pcr.idPersonalCargo = l.rGIdPersonalCargo
                        LEFT JOIN Haberes.Personal.HAPersonal ppr ON ppr.IdPersona = pcr.IdPersona
                        INNER JOIN Haberes.Haberes.HAConceptos co ON co.idconcepto = l.idconcepto
                        INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                        INNER JOIN LiquidacionesWeb.dbo.cupos c on c.id = ca.refCupo
                        INNER JOIN Haberes.General.HADependencias d on d.iddependencia = c.idDependencia
                        INNER JOIN LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refCupoAnual
                        INNER JOIN LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refCuenta "
                        ." WHERE ca.id= ".$refCupo
                        ." GROUP BY l.idPersonalCargo,ppr.apellido,ppr.nombre, d.iddependencia,ppr.legajo,l.rGIdPersonalCargo,d.dependencia,l.idConcepto,pp.apellido ,pp.nombre ,pp.legajo, l.rGFecha"
                        ." ORDER BY d.iddependencia, pp.apellido, pp.nombre";
                //die(var_dump($sql));

                $connection = $this -> getDoctrine()
                                        -> getManager("ms_haberes_web")
                                        -> getConnection();

                $agentes = $connection ->  prepare($sql);
                $agentes -> execute();


                //var_dump($agentes);
                //var_dump($lstAgentes);

                $columns = array(
                        new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                        new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'filter' => 'select', 'selectFrom' => 'query','operatorsVisible'=> false, 'title' => 'Apellido y Nombre','sortable' => false)),
                        new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'operatorsVisible'=> false, 'title' => 'Legajo')),
                        new Column\TextColumn(array('id' => 'concepto', 'field' => 'concepto','filterable' => false, 'source' => true, 'title' => 'Guardia')),
                        new Column\TextColumn(array('id' => 'reemplazado', 'field' => 'reemplazado','filterable' => false, 'source' => true, 'title' => 'Reemplazado')),
                        new Column\DateColumn(array('id' => 'fecha', 'field' => 'fecha', 'source' => true,'filterable' => false, 'title' => 'Fecha')),
                        new Column\NumberColumn(array('id' => 'cantidad', 'field' => 'cantidad', 'source' => true,'filterable' => false, 'title' => 'Cantidad')),
                        new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                        new Column\BooleanColumn(array('id'=>'requireautorizacion','field'=>'requireautorizacion','filterable' => false, 'source'=>true, 'title' => 'Require Aut.')),
                        new Column\TextColumn(array('id' => 'usuarioautoriza', 'field' => 'usuarioautoriza', 'source' => true,'filterable' => false, 'title' => 'Usua. Aut.')),
                        new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                    );
                break;
            case 'horas':
                $sqlAgentes = "SELECT   l.montoTotalCalculado as total,
                                    co.Nombre as concepto,
                                    l.idPersonalCargo as idpersonalcargo,
                                    pp.legajo,
                                    pp.apellido + ' ' + pp.nombre as apyn,
                                    l.hsExValorHora as valorhora,
                                    l.hsExCantSimples as simples,
                                    l.hsExCantDobles as dobles,
                                    l.id
                             FROM LiquidacionesWeb.dbo.liquidaciones l
                  INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                  INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                  INNER JOIN Haberes.Haberes.HAConceptos co ON co.IdConcepto = l.idConcepto
                  WHERE ca.refCupo= ".$refCupo;

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                $stmt = $conn->prepare($sqlAgentes);
                $stmt->execute();
                $agentes = 0;

                $agentes = $stmt->fetchAll();

                $columns = array(
                    new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                    new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true,'operatorsVisible'=> false,'filter' => 'select', 'selectFrom' => 'query', 'title' => 'Apellido y Nombre')),
                    new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true,'operatorsVisible'=> false, 'title' => 'Legajo')),
                    new Column\NumberColumn(array('id' => 'valorhora', 'field' => 'valorhora', 'source' => true,'filterable' => false, 'title' => 'Valor Hora')),
                    new Column\NumberColumn(array('id' => 'simples', 'field' => 'simples', 'source' => true,'filterable' => false, 'title' => 'Hs Simples')),
                    new Column\NumberColumn(array('id' => 'dobles', 'field' => 'dobles', 'source' => true,'filterable' => false, 'title' => 'Hs Dobles')),
                    new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                    new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                );
                break;
            default:
                $sqlAgentes = "SELECT   l.montoTotalCalculado as total,
                                    co.Nombre as concepto,
                                    l.idPersonalCargo as idpersonalcargo,
                                    pp.legajo,
                                    pp.apellido + ' ' + pp.nombre as apyn,
                                    l.id
                             FROM LiquidacionesWeb.dbo.liquidaciones l
                  INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                  INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                  INNER JOIN Haberes.Haberes.HAConceptos co ON co.IdConcepto = l.idConcepto
                  WHERE ca.refCupo= ".$refCupo;

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                $stmt = $conn->prepare($sqlAgentes);
                $stmt->execute();
                $agentes = 0;

                $agentes = $stmt->fetchAll();

                $columns = array(
                    new Column\NumberColumn(array('id' => 'idpersonalcargo','filterable' => false,'visible'=>false, 'field' => 'idpersonalcargo', 'source' => true,'primary' => true, 'title' => 'Id Persona')),
                    new Column\TextColumn(array('id' => 'apyn', 'field' => 'apyn', 'source' => true, 'title' => 'Apellido y Nombre')),
                    new Column\TextColumn(array('id' => 'legajo', 'field' => 'legajo', 'source' => true, 'title' => 'Legajo')),
                    new Column\NumberColumn(array('id' => 'total', 'field' => 'total', 'source' => true,'filterable' => false, 'title' => 'Importe')),
                    new Column\NumberColumn(array('id' => 'id', 'field' => 'id', 'source' => true,'filterable' => false,'visible'=>false, 'title' => 'ID')),
                );

                break;
        }

        return $agentes;
    }

    function devolverFechaCierre($refCupo) {
        //die(var_dump($refCupo));
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $HACupos    = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);
        //$fechaCierre = $em->getRepository('LiquidacionesCuposAnualesBundle:FechaCierre')->findBy(array("refCupo"=>$refCupo));

        $fechaCierre = $em->createQuery("SELECT l.id
                                         FROM LiquidacionesCuposAnualesBundle:FechaCierre l
                  WHERE l.refCupo= :idCupo and '".date('Y-m-d')."' >= l.fechaDesde and '".date('Y-m-d')."' <= l.fechaHasta")->setParameter('idCupo', $HACupos->getCupos()->getId())->getResult();


        if ($fechaCierre != null) {
            return 1;
        }
        return 0;


    }



    /**
     *
     * @Route("/traerValorHora/{tipobusqueda}/{busqueda}", name="liquidaciones_traerValorHora")
     */
    public function traerValorHoraAction($tipobusqueda,$busqueda)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        $sql     = "select
                        top 1
                        l.anio,l.mes
                    from        Haberes.Liquidacion.HALiquidacion l
                    where       l.IdTipoLiquidacion = 37 and l.adicional = 0
                    order by    1 desc,2 desc";

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $rResult = '';

        $rResult = $stmt->fetchAll();

        $anio = $rResult[0]['anio'];
        $mes  = $rResult[0]['mes'];

        switch ($tipobusqueda) {
            case 1:
                $sql = "select p.legajo as Legajo, p.Apellido, p.Nombre,p.NroDocumento,
                        convert(decimal(18,2),haberes.Haberes.fValorHoraLiquidado
                        ('".$mes."/01/".$anio."',refpersonalcargo)) as ValorHora, convert(varchar,dep.IdDependencia) + ' - ' + dep.Dependencia as depedencia

                        from haberes.Copia.HAPersonal p
                        inner join haberes.haberes.HAAgrupamiento a
                        on p.IdAgrupamiento=a.IdAgrupamiento
                        inner join haberes.haberes.HAPlanta pl
                        on pl.IdPlanta=p.IdPlanta
                        inner join haberes.General.HADependencias dep
                        on  dep.IdDependencia = p.IdDependenciaParte
                        where p.RefCabeceraCopia=4 -- ultima copia
                        and p.IdRegimenEstatutario=1
                        and p.FechaBajaPreventiva is null
                        and p.IdDependenciaPlantel = ".$usuarioDependencia."
                        group by p.legajo,p.NroDocumento, p.Apellido, p.Nombre, pl.Planta,
                        p.RefPersonalCargo, a.Descripcion,dep.IdDependencia, dep.Dependencia
                        order by Apellido, nombre";

                break;
            case 2:

                $sql = "select p.legajo as Legajo, p.Apellido, p.Nombre,p.NroDocumento,
                        convert(decimal(18,2),haberes.Haberes.fValorHoraLiquidado
                        ('".$mes."/01/".$anio."',refpersonalcargo)) as ValorHora, convert(varchar,dep.IdDependencia) + ' - ' + dep.Dependencia as depedencia

                        from haberes.Copia.HAPersonal p
                        inner join haberes.haberes.HAAgrupamiento a
                        on p.IdAgrupamiento=a.IdAgrupamiento
                        inner join haberes.haberes.HAPlanta pl
                        on pl.IdPlanta=p.IdPlanta
                        inner join haberes.General.HADependencias dep
                        on  dep.IdDependencia = p.IdDependenciaParte
                        where p.RefCabeceraCopia=4 -- ultima copia
                        and p.IdRegimenEstatutario=1
                        and p.FechaBajaPreventiva is null
                        and p.legajo = ".$busqueda."
                        group by p.legajo,p.NroDocumento, p.Apellido, p.Nombre, pl.Planta,
                        p.RefPersonalCargo, a.Descripcion,dep.IdDependencia, dep.Dependencia
                        order by Apellido, nombre";

                break;
            case 3:

                $sql = "select p.legajo as Legajo, p.Apellido, p.Nombre,p.NroDocumento,
                        convert(decimal(18,2),haberes.Haberes.fValorHoraLiquidado
                        ('".$mes."/01/".$anio."',refpersonalcargo)) as ValorHora, convert(varchar,dep.IdDependencia) + ' - ' + dep.Dependencia as depedencia

                        from haberes.Copia.HAPersonal p
                        inner join haberes.haberes.HAAgrupamiento a
                        on p.IdAgrupamiento=a.IdAgrupamiento
                        inner join haberes.haberes.HAPlanta pl
                        on pl.IdPlanta=p.IdPlanta
                        inner join haberes.General.HADependencias dep
                        on  dep.IdDependencia = p.IdDependenciaParte
                        where p.RefCabeceraCopia=4 -- ultima copia
                        and p.IdRegimenEstatutario=1
                        and p.FechaBajaPreventiva is null
                        and p.NroDocumento = ".$busqueda."
                        group by p.legajo,p.NroDocumento, p.Apellido, p.Nombre, pl.Planta,
                        p.RefPersonalCargo, a.Descripcion,dep.IdDependencia, dep.Dependencia
                        order by Apellido, nombre";

                break;
            default:
                $sql     = "";
                break;
        }


        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $rResult = '';

        $rResult = $stmt->fetchAll();

        $ar = array();

        //die(var_dump($rResult[0]['IdImputacionPresupuestaria']));
        $vowels = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú");
        $replace = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U");

        if (!(is_array($rResult))) {
            $ar = array('legajo'=> 0, 'apellido'=> '', 'nombre'=> '', 'nrodocumento'=> '', 'valorhora'=> 0);
        } else {
            foreach ($rResult as $value) {

                array_push($ar,array('legajo'=> $value['Legajo'], 'apellido'=> utf8_encode($value['Apellido']), 'nombre'=> utf8_encode($value['Nombre']), 'nrodocumento'=> $value['NroDocumento'], 'valorhora'=> $value['ValorHora']));

            }
        }
        $response = new Response(json_encode($ar));
        $response->headers->set('content-type','application/json');

        return $response;

    }



    /**
     *
     * @Route("/exportarExcel/{refCupo}", name="cupos_exportarExcel")
     */
    public function rptExcelAction($datos, $cabecerasDatos, $cabeceras, $titulo, Request $request)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $lstDatos[] = null;

        $response = new Response();
        $excel = new \PHPExcel();

        $excel->getProperties()
            ->setCreator("Admin")
            ->setTitle("Mesajlar - ".date('d.m.Y'))
            ->setSubject("Mesajlar - ".date('d.m.Y'));

        $index = 2;

        $exportFileName = $titulo;

        $letras = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        for($i=0;$i<count($cabeceras);$i++) {
            $excel->setActiveSheetIndex(0)->setCellValue($letras[$i].'1', $cabeceras[$i]);
        }

        $fechas = '';
        // Agrego los datos
        foreach($datos as $agente)
        {
            for($i=0;$i<count($cabecerasDatos);$i++) {

              if (($cabecerasDatos[$i] == 'fechaDesde') || ($cabecerasDatos[$i] == 'fechaHasta')) {
                if ($agente[$cabecerasDatos[$i]] == '') {
                  $fechas = '';
                } else {
                  $fechas = $agente[$cabecerasDatos[$i]]->format('Y-m-d H:i:s');
                }
                $excel->setActiveSheetIndex(0)->setCellValue($letras[$i].$index, $fechas);
              } else {
                $excel->setActiveSheetIndex(0)->setCellValue($letras[$i].$index, utf8_encode($agente[$cabecerasDatos[$i]]));
              }
            }

            $index++;
        }

        // Set active sheet index to the first sheet
        $excel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel5)
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$exportFileName.'.xls"');
        $response->headers->set('Cache-Control', 'max-age=0');
        $response->prepare($request);
        $response->sendHeaders();
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');
        exit();

    }

    /**
     *
     * @Route("/generarreporte", name="generarreporte")
     * @Template("LiquidacionesCuposAnualesBundle:Liquidaciones:reportes.html.twig")
     */
    public function generarreportesAction(Request $request)
    {
        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        $data = $request->request->all();

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $tiporeporte    =   $data["tiporeporte"];
        switch ($tiporeporte) {
            case 1:
                $iddependencia =    $data['iddependencia'];

                $sql = "EXEC partenovedades.[dbo].[traeParteProvisorio] ".$iddependencia.", 3";

                $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

                $res = $connection ->  prepare($sql);

                $res -> execute();

                if (count($res)>0) {
                    $titulo = "Parte de Novedades: ";

                    $cabeceras = array('MES','AÑO','LEGAJO','APELLIDO','NOMBRE','FUNCION','REG HORARIO','AGRUPAMIENTO','D1','D2','D3','D4','D5','D6','D7','D8','D9','D10','D11','D12','D13','D14','D15','D16','D17','D18','D19','D20','D21','D22','D23','D24','D25','D26','D27','D28','D29','D30','D31');
                    $cabecerasDatos = array('mes','anio','legajo','apellido','nombre','funcion','regHorario','agrupamiento','d1','d2','d3','d4','d5','d6','d7','d8','d9','d10','d11','d12','d13','d14','d15','d16','d17','d18','d19','d20','d21','d22','d23','d24','d25','d26','d27','d28','d29','d30','d31');

                    $this->rptExcelAction($res, $cabecerasDatos, $cabeceras, $titulo, $request);
                } else {

                    $this->getRequest()->getSession()->getFlashBag()->add('aviso_error', 'No hay agentes en esta Cuenta para el periodo solicitado.');
                }
            break;
            case 2:
                $iddependencia =    $data['iddependencia'];
                $idcupoanual =      $data['idcupoanual'];
                $mes =              $data['mes'];
                $anio =             $data['anio'];
                $idcuenta =         $data['idcuenta'];

                $sql = "EXEC LiquidacionesWeb.[dbo].[RG_LiquidadoPorDependencia] ".$iddependencia.",".$mes.",".$anio.",".$idcupoanual.",".$idcuenta;

                $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

                $res = $connection ->  prepare($sql);

                $res -> execute();

                if (count($res)>0) {
                    $titulo = "Listado nominalizado de lo liquidado por dependencia: ";

                    $cabeceras = array('DEPENDENCIA','NOMBRE_DEPENDENCIA','AÑO','MES','REEMPLAZANTE','REEMPLAZADO','FECHA_REEMPLAZO','CODIGO','NOMBRE','MONTO','USUARIO_CARGA','FECHA_CARGA');
                    $cabecerasDatos = array('DEPENDENCIA','NOMBRE_DEPENDENCIA','ANIO','MES','REEMPLAZANTE','REEMPLAZADO','FECHA_REEMPLAZO','CODIGO','NOMBRE','MONTO','USUARIO_CARGA','FECHA_CARGA');

                    $this->rptExcelAction($res, $cabecerasDatos, $cabeceras, $titulo, $request);
                } else {

                    $this->getRequest()->getSession()->getFlashBag()->add('aviso_error', 'No hay agentes en esta Cuenta para el periodo solicitado.');
                }
                break;
            case 3:
                $iddependencia =    $data['iddependencia'];
                $idcupoanual =      $data['idcupoanual'];
                $mes =              $data['mes'];
                $anio =             $data['anio'];
                $idcuenta =         $data['idcuenta'];

                $sql = "EXEC LiquidacionesWeb.[dbo].[RG_CargadoPorDependencia] ".$iddependencia.",".$mes.",".$anio.",".$idcupoanual.",".$idcuenta;

                $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

                $res = $connection ->  prepare($sql);

                $res -> execute();

                if (count($res)>0) {
                    $titulo = "Listado nominalizado de lo liquidado por dependencia: ";

                    $cabeceras = array('DEPENDENCIA','NOMBRE_DEPENDENCIA','AÑO','MES','REEMPLAZANTE','REEMPLAZADO','FECHA_REEMPLAZO','CODIGO','NOMBRE','MONTO','USUARIO_CARGA','FECHA_CARGA');
                    $cabecerasDatos = array('DEPENDENCIA','NOMBRE_DEPENDENCIA','ANIO','MES','REEMPLAZANTE','REEMPLAZADO','FECHA_REEMPLAZO','CODIGO','NOMBRE','MONTO','USUARIO_CARGA','FECHA_CARGA');

                    $this->rptExcelAction($res, $cabecerasDatos, $cabeceras, $titulo, $request);
                } else {

                    $this->getRequest()->getSession()->getFlashBag()->add('aviso_error', 'No hay agentes en esta Cuenta para el periodo solicitado.');
                }
                break;
            case 4:
                $iddependencia =    $data['iddependencia'];
                $idcupoanual =      $data['idcupoanual'];
                $mes =              $data['mes'];
                $anio =             $data['anio'];
                $idcuenta =         $data['idcuenta'];

                $sql = "EXEC LiquidacionesWeb.[dbo].[RG_VacantesYTotalLicenciasPorDepeCargado] ".$iddependencia.",".$mes.",".$anio.",".$idcupoanual.",".$idcuenta;

                $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

                $res = $connection ->  prepare($sql);

                $res -> execute();

                if (count($res)>0) {
                    $titulo = "Listado nominalizado de lo liquidado por dependencia: ";

                    $cabeceras = array('AÑO','MES','DEPENDENCIA','NOMBRE_DEPENDENCIA','TOTAL_RG','TOTAL_LICENCIA','TOTAL_VACANTE');
                    $cabecerasDatos = array('ANIO','MES','DEPENDENCIA','NOMBRE_DEPENDENCIA','TotalRg','TotalLicencia','TotalVacante');

                    $this->rptExcelAction($res, $cabecerasDatos, $cabeceras, $titulo, $request);
                } else {

                    $this->getRequest()->getSession()->getFlashBag()->add('aviso_error', 'No hay agentes en esta Cuenta para el periodo solicitado.');
                }
                break;
             case 5:

                $sql = "EXEC [LiquidacionesWeb].[dbo].[Reporte_DepeParte_10430yBecarios_H]";

                $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

                $res = $connection ->  prepare($sql);

                $res -> execute();

                if (count($res)>0) {
                    $titulo = "Reporte por Dependencia de Parte - Personal Ley 10430 - Hospitales ";

                    $cabeceras = array('CodigoDepe','Dependencia','cantidadTotal10430','cantidadTotalBeca');
                    $cabecerasDatos = array('CodigoDepe','Dependencia','cantidadTotal10430','cantidadTotalBeca');

                    $this->rptExcelAction($res, $cabecerasDatos, $cabeceras, $titulo, $request);
                } else {

                    $this->getRequest()->getSession()->getFlashBag()->add('aviso_error', 'No hay agentes en esta Cuenta para el periodo solicitado.');
                }
            break;
            case 6:

                $sql = "EXEC [Haberes].[web].[splistadobajasLiquidacionesWeb] null";

                $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

                $res = $connection ->  prepare($sql);

                $res -> execute();

                if (count($res)>0) {
                    $titulo = "Reporte de Bajas de Cargos de Medicos ";

                    $cabeceras = array('Ley','Agrupamiento','Encasillamiento','Legajo','NroDocumento','ApellidoYNombre','DependenciaCobro','fechatomaposesion','fechabajapreventiva','motivobaja','Guardia');
                    $cabecerasDatos = array('Ley','Agrupamiento','Encasillamiento','Legajo','NroDocumento','ApellidoYNombre','DependenciaCobro','fechatomaposesion','fechabajapreventiva','motivobaja','Guardia');

                    $this->rptExcelAction($res, $cabecerasDatos, $cabeceras, $titulo, $request);
                } else {

                    $this->getRequest()->getSession()->getFlashBag()->add('aviso_error', 'No hay agentes en esta Cuenta para el periodo solicitado.');
                }
            break;
            default:
                # code...
                break;
        }

        $entityCupos    = $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos');
        $entityAnual    = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposAnuales');

        $form = $entityCupos->createQueryBuilder('c')
        ->select('c')
        ->innerJoin('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion', 'co', 'WITH', 'co.refCupo = c.id')
        ->orderBy('c.id', 'ASC')
        ->where('co.refCuenta in (22,23,24,25)')
        ->getQuery()
        ->getResult();

        $queryAnual = $entityAnual->createQueryBuilder('a')
        ->select('a')
        ->innerJoin('LiquidacionesCuposAnualesBundle:Cupos', 'c', 'WITH', 'c.refCupoAnual = a.id')
        ->innerJoin('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion', 'co', 'WITH', 'co.refCupo = c.id')
        ->where('co.refCuenta in (22,23,24,25)')
        ->groupBy('a')
        ->orderBy('a.id', 'ASC')
        ->getQuery()
        ->getResult();

        $queryAnualAnios = $entityAnual->createQueryBuilder('a')
        ->select('a.anio')
        ->innerJoin('LiquidacionesCuposAnualesBundle:Cupos', 'c', 'WITH', 'c.refCupoAnual = a.id')
        ->innerJoin('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion', 'co', 'WITH', 'co.refCupo = c.id')
        ->where('co.refCuenta in (22,23,24,25)')
        ->groupBy('a.anio')
        ->orderBy('a.anio', 'DESC')
        ->getQuery()
        ->getResult();

        $formC   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findBy(array('esPresupuestaria'=>'0'));
        $formCP   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findBy(array('id'=>array(22,23,24,25)));


        $sql = "SELECT distinct d.IdDependencia, d.Dependencia
                    FROM Haberes.General.HADependencias d
                    inner join LiquidacionesWeb.dbo.cupos c ON c.iddependencia = d.IdDependencia
                    inner join LiquidacionesWeb.dbo.cuposhatiposliquidacion ca ON ca.refcupo = c.id
                    where ca.refcuenta in (22,23,24,25)
                    order by d.IdDependencia ";

        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $entityHAL = $connection ->  prepare($sql);

        $entityHAL -> execute();

        return array(
            'form'   => $form,
            'formCP'   => $formCP,
            'formC'   => $formC,
            'entityHAL' => $entityHAL,
            'anual' => $queryAnual,
            'meses' => array(1,2,3,4,5,6,7,8,9,10,11,12),
            'anios' => $queryAnualAnios,
        );

    }

    /**
     *
     * @Route("/reportes", name="liquidaciones_reportes")
     * @Template("LiquidacionesCuposAnualesBundle:Liquidaciones:reportes.html.twig")
     */
    public function reportesAction()
    {
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        // Creo el formulario de búsqueda
        //$entity     = new Cupos();
        //$cuentas    = new Cuentas();

        $entityCupos    = $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos');
        $entityAnual    = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposAnuales');

        $form = $entityCupos->createQueryBuilder('c')
        ->select('c')
        ->innerJoin('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion', 'co', 'WITH', 'co.refCupo = c.id')
        ->orderBy('c.id', 'ASC')
        ->where('co.refCuenta in (22,23,24,25)')
        ->getQuery()
        ->getResult();

        $queryAnual = $entityAnual->createQueryBuilder('a')
        ->select('a')
        ->innerJoin('LiquidacionesCuposAnualesBundle:Cupos', 'c', 'WITH', 'c.refCupoAnual = a.id')
        ->innerJoin('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion', 'co', 'WITH', 'co.refCupo = c.id')
        ->where('co.refCuenta in (22,23,24,25)')
        ->groupBy('a')
        ->orderBy('a.id', 'ASC')
        ->getQuery()
        ->getResult();

        $queryAnualAnios = $entityAnual->createQueryBuilder('a')
        ->select('a.anio')
        ->innerJoin('LiquidacionesCuposAnualesBundle:Cupos', 'c', 'WITH', 'c.refCupoAnual = a.id')
        ->innerJoin('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion', 'co', 'WITH', 'co.refCupo = c.id')
        ->where('co.refCuenta in (22,23,24,25)')
        ->groupBy('a.anio')
        ->orderBy('a.anio', 'DESC')
        ->getQuery()
        ->getResult();

        $formC   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findBy(array('esPresupuestaria'=>'0'));
        $formCP   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findBy(array('id'=>array(22,23,24,25)));


        $sql = "SELECT distinct d.IdDependencia, d.Dependencia
                    FROM Haberes.General.HADependencias d
                    inner join LiquidacionesWeb.dbo.cupos c ON c.iddependencia = d.IdDependencia
                    inner join LiquidacionesWeb.dbo.cuposhatiposliquidacion ca ON ca.refcupo = c.id
                    where ca.refcuenta in (22,23,24,25)
                    order by d.IdDependencia ";

        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $entityHAL = $connection ->  prepare($sql);

        $entityHAL -> execute();

        return array(
            'form'   => $form,
            'formCP'   => $formCP,
            'formC'   => $formC,
            'entityHAL' => $entityHAL,
            'anual' => $queryAnual,
            'meses' => array(1,2,3,4,5,6,7,8,9,10,11,12),
            'anios' => $queryAnualAnios,
        );

    }


    /**
     *
     * @Route("/subirarchivo/{refCupo}", name="liquidaciones_subirarchivo")
     * @Template("LiquidacionesCuposAnualesBundle:Liquidaciones:subirarchivo.html.twig")
     * @Method("GET")
     */
    public function subirarchivoAction($refCupo)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $entity = new Liquidaciones();

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);

        $nombrecuenta = '';
        foreach ($entities as $cupo) {
            $cuenta = $cupo->getCuentas()->getModoCarga();
            $HAcupo = $cupo->getId();
            $nombrecuenta .= $cupo->getCuentas()->getCuenta().' ';
            $idtipoliquidacion = $cupo->getCuentas()->getIdTipoLiquidacion();
        }


        $appPath = $this->container->getParameter('kernel.root_dir');
        $webPath = realpath($appPath . '/../web/uploads/planillas');

        $path  = $webPath.'/'.$refCupo;
        //die(var_dump($path));
        //mkdir($path, 0777);
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }

        $filesCargados = array_diff(scandir($path), array('.', '..'));

        $existeArchivo = 0;
        $nombrearchivo = '';
        if (count($filesCargados)>0) {
            $existeArchivo = 1;
            //die(var_dump($filesCargados));
            $nombrearchivo = $filesCargados['2'];
        }


        return array(
            'refCupo' => $refCupo,
            'nombreCuenta' => $nombrecuenta,
            'existeArchivo' => $existeArchivo,
            'nombrearchivo' => $nombrearchivo,
        );
    }

    /**
     *
     * @Route("/subir", name="liquidaciones_subir")
     */
    public function subirAction(Request $request)
    {

        $file = $request->files->get('image');

        $data = $request->request->all();

        $refCupo = $data['refCupo'];

        if (!(isset($file))) {
            $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'Debe seleccionar un archivo .pdf para cargar.'
            );
            return $this->redirect($this->generateUrl('liquidaciones_subirarchivo',array('refCupo' => $refCupo)));
        } else {
            //die(var_dump($file->getClientSize()));
            if ($file->getClientSize() > 2000000) {
                $this->getRequest()->getSession()->getFlashBag()->add(
                'aviso_error',
                'Recuerde que el archivo no puede superar los 2 MB de peso.'
            );
            return $this->redirect($this->generateUrl('liquidaciones_subirarchivo',array('refCupo' => $refCupo)));
            }
        }

        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $entity = new Liquidaciones();

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($refCupo);

        $appPath = $this->container->getParameter('kernel.root_dir');
        $webPath = realpath($appPath . '/../web/uploads/planillas');

        $path  = $webPath.'/'.$refCupo;

        $filesCargados = array_diff(scandir($path), array('.', '..'));

        $existeArchivo = 0;
        if (count($filesCargados)>0) {
            array_map('unlink', glob($path."/*.*"));
        }


        $nombrecuenta = '';
        foreach ($entities as $cupo) {
            $cuenta = $cupo->getCuentas()->getModoCarga();
            $HAcupo = $cupo->getId();
            $nombrecuenta .= $cupo->getCuentas()->getCuenta().' ';
            $idtipoliquidacion = $cupo->getCuentas()->getIdTipoLiquidacion();
        }

        $appPath = $this->container->getParameter('kernel.root_dir');
        $webPath = realpath($appPath . '/../web/uploads/planillas');

        $path  = $webPath.'/'.$refCupo;

        $filesCargados = array_diff(scandir($path), array('.', '..'));

        $existeArchivo = 0;
        $nombrearchivo = '';
        if (count($filesCargados)>0) {
            $existeArchivo = 1;
            $nombrearchivo = $filesCargados[2];
        }

        $fileName = md5(uniqid()).'.pdf';
        $cvDir = $this->container->getparameter('kernel.root_dir').'/../web/uploads/planillas/'.$refCupo;
        $file->move($cvDir, $fileName);

        return $this->redirect($this->generateUrl('liquidaciones_subirarchivo',array('refCupo' => $refCupo)));
    }



}
