<?php

namespace Liquidaciones\CuposAnualesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\CuposAnualesBundle\Entity\Cupos;
use Liquidaciones\CuposAnualesBundle\Entity\CuposAnuales;
use Liquidaciones\CuposAnualesBundle\Entity\Cuentas;
use Liquidaciones\CuposAnualesBundle\Entity\CuposHATiposLiquidacion;
use Liquidaciones\CuposAnualesBundle\Form\CuposType;
use Liquidaciones\CuposAnualesBundle\Form\CuentasType;
use Liquidaciones\CuposAnualesBundle\Helpers\DataTable;
use Liquidaciones\ReferenciasBundle\Entity\TipoDependencia;
use Liquidaciones\ReferenciasBundle\Controller\ImputacionpresupuestariaController;
use Liquidaciones\HaberesBundle\Entity\HADependencias;
use Liquidaciones\HaberesBundle\Entity\HAImputacionPresupuestaria;
use Symfony\Component\HttpFoundation\Response;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Source\Vector;
use APY\DataGridBundle\Grid\Column;
use APY\DataGridBundle\Grid\Export\CSVExport;
use APY\DataGridBundle\Grid\Export\ExcelExport;
use APY\DataGridBundle\Grid\Export\PHPExcelPDFExport;
use APY\DataGridBundle\Grid\Export\MSExportarExcel;
use APY\DataGridBundle\Grid\Action\MassAction;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

use \PDO;
/**
 * Cupos controller.
 *
 * @Route("/cupos")
 */
class CuposController extends Controller
{

    /**
     * Lists all Cupos entities.
     *
     * @Route("/", name="cupos")
     * @Template()
     */
    public function indexAction()
    {
        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((false === $securityContext->isGranted('ROLE_15')) || (false === $securityContext->isGranted('ROLE_21'))) {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $sql = "SELECT c.id,
                            c.anio,
                            (RIGHT('00' + convert(varchar(2),c.mes),2)) as mes,
                            c.monto,
                            isnull(l.montoLiquidado,0) as montoLiquidado,
                            isnull(ROUND(((100 * l.montoLiquidado) / c.monto),2),0) as porcentaje,
                            e.cupoEstado,
                            (RIGHT('0000' + convert(varchar(4),d.iddependencia),4) + '-' + d.dependencia) as codigo,
                            cc.cuenta,
                            ca.adicional,
                            a.descripcion,
                            d.RegSan1 as COD_TIPO_DEPENDENCIA
                            ,fec.fechaHasta
                    FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                  inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                  inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                  inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                  inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                  inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta and cc.id in (1,6,7,8,9,10,11,15,16,17,21,22,23,24,25,26)
                  left join LiquidacionesWeb.dbo.FechaCierre fec on          fec.refCupo = c.id
                  left join (select sum(ll.montoTotalCalculado) as montoLiquidado
                                     ,ll.refCupoTipoLiquidacion
                                from LiquidacionesWeb.dbo.liquidaciones ll

                                group by ll.refCupoTipoLiquidacion) l on ca.id = l.refCupoTipoLiquidacion
                  where cc.id not in (2,3,18,19,22,23) and convert(date, convert(varchar,c.mes) + '/01' +  '/' + convert(varchar,c.anio)) > DATEADD(MM, -5, getdate())

union all


        select  d.id,
                d.anio,
                (RIGHT('00' + convert(varchar(2),d.mes),2)) as mes,
                d.monto,
                isnull(sum(d.montoLiquidado),0) as montoLiquidado,
                sum(d.porcentaje) as porcentaje,
                d.cupoEstado,
                d.codigo,
                max(d.cuentaModulo) + ' / ' + max(d.cuentaBeca) as cuenta,
                d.adicional,
                d.descripcion,
                d.COD_TIPO_DEPENDENCIA,
                d.fechaHasta

                from (
                SELECT c.id,
                            c.anio,
                            c.mes,
                            c.monto,
                            l.montoLiquidado,
                            isnull(ROUND(((100 * l.montoLiquidado) / c.monto),2),0) as porcentaje,
                            e.cupoEstado,
                            (RIGHT('0000' + convert(varchar(4),d.iddependencia),4) + '-' + d.dependencia) as codigo,
                            cc.cuenta as cuentaModulo,
                            '' as cuentaBeca,
                            ca.adicional,
                            a.descripcion,
                            d.RegSan1 as COD_TIPO_DEPENDENCIA
                            ,fec.fechaHasta
                    FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                  inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                  inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                  inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                  inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                  inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta and cc.id in (1,6,7,8,9,10,11,15,16,17,21,22,23,24,25,26)
                  left join LiquidacionesWeb.dbo.FechaCierre fec on          fec.refCupo = c.id
                  left join (select sum(ll.montoTotalCalculado) as montoLiquidado
                                     ,ll.refCupoTipoLiquidacion
                                from LiquidacionesWeb.dbo.liquidaciones ll

                                group by ll.refCupoTipoLiquidacion) l on ca.id = l.refCupoTipoLiquidacion
                  where cc.id in (2,18,22) and convert(date, convert(varchar,c.mes) + '/01' +  '/' + convert(varchar,c.anio)) > DATEADD(MM, -5, getdate())

                union all

                SELECT c.id,
                            c.anio,
                            c.mes,
                            c.monto,
                            l.montoLiquidado,
                            isnull(ROUND(((100 * l.montoLiquidado) / c.monto),2),0) as porcentaje,
                            e.cupoEstado,
                            (RIGHT('0000' + convert(varchar(4),d.iddependencia),4) + '-' + d.dependencia) as codigo,
                            '' as cuentaModulo,
                            cc.cuenta as cuentaBeca,
                            ca.adicional,
                            a.descripcion,
                            d.RegSan1 as COD_TIPO_DEPENDENCIA
                            ,fec.fechaHasta
                    FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                  inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                  inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                  inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                  inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                  inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta and cc.id in (1,6,7,8,9,10,11,15,16,17,21,22,23,24,25,26)
                  left join LiquidacionesWeb.dbo.FechaCierre fec on          fec.refCupo = c.id
                  left join (select sum(ll.montoTotalCalculado) as montoLiquidado
                                     ,ll.refCupoTipoLiquidacion
                                from LiquidacionesWeb.dbo.liquidaciones ll

                                group by ll.refCupoTipoLiquidacion) l on ca.id = l.refCupoTipoLiquidacion
                  where cc.id in (3,19,23)  and convert(date, convert(varchar,c.mes) + '/01' +  '/' + convert(varchar,c.anio)) > DATEADD(MM, -5, getdate()) ) d
            group by d.anio,
                d.mes,
                d.monto,
                d.cupoEstado,
                d.adicional,
                d.codigo,
                d.descripcion
                ,d.id,
                d.fechaHasta,
                d.COD_TIPO_DEPENDENCIA";

        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $entities = $connection ->  prepare($sql);

        $entities -> execute();

        $sql =       "select cc.id as idcuenta,cc.cuenta,a.descripcion"
                        ." FROM        LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion ca "
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
                        ." WHERE e.id <> 4 GROUP BY cc.id,cc.cuenta,a.descripcion";


        $resCuentas = $em->createQuery($sql)->getResult();


        if (($securityContext->isGranted('ROLE_16')) || ($securityContext->isGranted('ROLE_17'))) {
            $puedeModificar = 1;
        } else {
            $puedeModificar = 0;
        }

        if ($securityContext->isGranted('ROLE_19')) {
            $liquida = 1;
        } else {
            $liquida = 0;
        }
        return array(
            'entities' => $entities,
            'usuaModifica'=>$puedeModificar,
            'puedeLiquidar'=>$liquida,
            'rescuentas'=>$resCuentas,
        );
    }



    /**
     * Lists all Cupos entities.
     *
     * @Route("/gastado", name="cupos_gastado")
     * @Template()
     */
    public function gastadoAction()
    {
        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((false === $securityContext->isGranted('ROLE_15')) || (false === $securityContext->isGranted('ROLE_21'))) {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $sql = "SELECT c.id,
                            c.anio,
                            c.mes,
                            c.monto,
                            l.montoLiquidado,
                            isnull(ROUND(((100 * l.montoLiquidado) / c.monto),2),0) as porcentaje,
                            e.cupoEstado,
                            convert(varchar(5),d.iddependencia) + ' - ' + d.dependencia as codigo,
                            cc.cuenta,
                            ca.adicional,
                            a.descripcion,
                            d.RegSan1 as COD_TIPO_DEPENDENCIA
                    FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                  inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                  inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                  inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                  inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                  inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                  left join (select sum(ll.montoTotalCalculado) as montoLiquidado
                                     ,ll.refCupoTipoLiquidacion
                                from LiquidacionesWeb.dbo.liquidaciones ll

                                group by ll.refCupoTipoLiquidacion) l on ca.id = l.refCupoTipoLiquidacion
                  where cc.id not in (2,3,18,19,22,23) and e.id in (2,3)

                union all


        select  d.id,
                d.anio,
                d.mes,
                d.monto,
                d.montoLiquidado,
                d.porcentaje,
                d.cupoEstado,
                d.codigo,
                min(d.cuentaModulo) + ' / ' + min(d.cuentaBeca) as cuenta,
                d.adicional,
                d.descripcion,
                d.COD_TIPO_DEPENDENCIA

                from (
                SELECT c.id,
                            c.anio,
                            c.mes,
                            c.monto,
                            l.montoLiquidado,
                            isnull(ROUND(((100 * l.montoLiquidado) / c.monto),2),0) as porcentaje,
                            e.cupoEstado,
                            convert(varchar(5),d.iddependencia) + ' - ' + d.dependencia as codigo,
                            cc.cuenta as cuentaModulo,
                            '' as cuentaBeca,
                            ca.adicional,
                            a.descripcion,
                            d.RegSan1 as COD_TIPO_DEPENDENCIA
                    FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                  inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                  inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                  inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                  inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                  inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                  left join (select sum(ll.montoTotalCalculado) as montoLiquidado
                                     ,ll.refCupoTipoLiquidacion
                                from LiquidacionesWeb.dbo.liquidaciones ll

                                group by ll.refCupoTipoLiquidacion) l on ca.id = l.refCupoTipoLiquidacion
                  where cc.id in (2,18,22) and e.id in (2,3)

                union all

                SELECT c.id,
                            c.anio,
                            c.mes,
                            c.monto,
                            l.montoLiquidado,
                            isnull(ROUND(((100 * l.montoLiquidado) / c.monto),2),0) as porcentaje,
                            e.cupoEstado,
                            convert(varchar(5),d.iddependencia) + ' - ' + d.dependencia as codigo,
                            '' as cuentaModulo,
                            cc.cuenta as cuentaBeca,
                            ca.adicional,
                            a.descripcion,
                            d.RegSan1 as COD_TIPO_DEPENDENCIA
                    FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                  inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                  inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                  inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                  inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                  inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                  left join (select sum(ll.montoTotalCalculado) as montoLiquidado
                                     ,ll.refCupoTipoLiquidacion
                                from LiquidacionesWeb.dbo.liquidaciones ll

                                group by ll.refCupoTipoLiquidacion) l on ca.id = l.refCupoTipoLiquidacion
                  where cc.id in (3,19,23) and e.id in (2,3)) d
            group by d.anio,
                d.mes,
                d.monto,
                d.cupoEstado,
                d.adicional,
                d.codigo,
                d.descripcion
                ,d.id,
                d.montoLiquidado,
                d.porcentaje,
                d.COD_TIPO_DEPENDENCIA
            ";

        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $entities = $connection ->  prepare($sql);

        $entities -> execute();

        if (($securityContext->isGranted('ROLE_16')) || ($securityContext->isGranted('ROLE_17'))) {
            $puedeModificar = 1;
        } else {
            $puedeModificar = 0;
        }

        if ($securityContext->isGranted('ROLE_19')) {
            $liquida = 1;
        } else {
            $liquida = 0;
        }
        return array(
            'entities' => $entities,
            'usuaModifica'=>$puedeModificar,
            'puedeLiquidar'=>$liquida,
        );
    }




    /**
     * Creates a new Cupos entity.
     *
     * @Route("/create", name="cupos_create")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:new.html.twig")
     */
    public function createAction(Request $request)
    {

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        $usr = $this->get('security.context')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $sql = "SELECT distinct d.RegSan1 as COD_TIPO_DEPENDENCIA
                    FROM Haberes.General.HADependencias d where d.RegSan1 is not null";

        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $entityHAL = $connection ->  prepare($sql);

        $entityHAL -> execute();

        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {

            $entity = new Cupos();

            //  var_dump($data);

            $form = $this->createCreateForm($entity);
            $form->handleRequest($request);

            //$formC = $this->createCreateFormCuentas($cuentas);
            $data = $request->request->all();

            $entity->setIdDependencia((integer)$data['idDependencia']);
            $entity->setUsuaCrea($usr->getUsername());
            $entity->setUsuaModi($usr->getUsername());
            //$datetime = new \DateTime('Y-m-d H:i:s');
            $entity->setFechaCrea(new \DateTime);
            $entity->setFechaModi(new \DateTime);

            //return var_dump($data);
            //$name = $data['form']['name'];

            $lstCuentas = explode(',',$data['lstCuentasAgregadas']);
            //die(var_dump($data));
            //$formC->handleRequest($request);
            if (isset($data['imputacion'])) {
                $idImputacionPresupuestaria = $data['imputacion'];
            } else {
                $idImputacionPresupuestaria = '';
            }
            if ($idImputacionPresupuestaria == "") {

                $direccion = "cupos_new";
                $request->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'Debe ingresar una imputación presupuestaria para poder guardar. '
                );
                return $this->redirect($this->generateUrl($direccion));
            }

            $total = $em->createQuery('SELECT sum(c.monto) as total FROM LiquidacionesCuposAnualesBundle:Cupos c
                      JOIN c.cuposanuales ca
                      WHERE ca.id= :idCupoAnual')->setParameter('idCupoAnual', $entity->getCuposanuales()->getId())->getResult();
            $totalCupoMensual = $total[0]["total"];

            $entity->setMonto((float)str_replace(',','.',$entity->getMonto()));

            if (($entity->getCuposanuales()->getMonto() - $totalCupoMensual - $entity->getMonto()) < 0) {
                $direccion = "cupos_new";
                $request->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El monto del cupo mensual cargado no puede superar la diferencia entre el Monto del Cupo Anual y sumatoria de los cupos mensuales ya cargados. Saldo del Cupo Anual "'.$entity->getCuposanuales()->getDescripcion().'": $'.($entity->getCuposanuales()->getMonto() - $totalCupoMensual)
                );
                return $this->redirect($this->generateUrl($direccion));
            }

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager("ms_haberes_web");

                $em->persist($entity);
                $em->flush();

                foreach($lstCuentas as $idcuenta) {
                    $cuentas = new Cuentas();
                    $cuentas = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($idcuenta);

                    $cuposHATL = new CuposHATiposLiquidacion();

                    //validar adicional
                    $sql =   "select isnull(max(ca.adicional),-1) as adicional "
                            ." FROM        LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                                inner join LiquidacionesWeb.dbo.cupos c on ca.refcupo= c.id ";
                    $sql = $sql." WHERE c.anio = ".$entity->getAnio()." and c.mes = ".$entity->getMes()." and ca.refCuenta = ".$idcuenta."and c.idDependencia = ".$entity->getIdDependencia();

                    $stmt = $connection->prepare($sql);

                    $stmt->execute();
                    $resAdicional = 0;
                    $resAdicional = $stmt->fetchAll();

                    if ($resAdicional[0]['adicional'] == 0) {
                        $adicional = 2;
                    } else {
                        if ($resAdicional[0]['adicional'] > 2) {
                            $adicional = $resAdicional[0]['adicional'] + 1;
                        } else {
                            $adicional = 0;
                        }
                    }

                    //die(var_dump($adicional));
                    $cuposHATL->setAdicional($adicional);
                    $cuposHATL->setCuentas($cuentas);
                    if ($idcuenta == 7) {
                      $cuposHATL->setIdImputacionPresupuestaria(2231);
                    } else {
                      $cuposHATL->setIdImputacionPresupuestaria($idImputacionPresupuestaria);
                    }

                    $cuposHATL->setRefCuenta($idcuenta);
                    $cuposHATL->setCupos($entity);
                    $cuposHATL->setRefCupo($entity->getId());

                    $em->persist($cuposHATL);
                    $em->flush();
                }

                $request->getSession()->getFlashBag()->add(
                    'aviso_ok',
                    'Los datos fueron guardados correctamente!'
                );
                return $this->redirect($this->generateUrl('cupos_new',array('idCupoAnual' => $entity->getCuposanuales()->getId(),)));

            } else {

                $request->getSession()->getFlashBag()->add(
                    'aviso_error',
                    $form->getErrorsAsString()
                );

            }



            return array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'entityHAL' => $entityHAL,

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
    * Creates a form to create a Cupos entity.
    *
    * @param Cupos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Cupos $entity)
    {
        $form = $this->createForm(new CuposType(), $entity, array(
            'action' => $this->generateUrl('cupos_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }


    /**
    * Creates a form to create a Cupos entity.
    *
    * @param Cupos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function cuentascreateCreateForm(Cuentas $entity)
    {
        $form = $this->createForm(new CuentasType(), $entity, array(
            'action' => $this->generateUrl('cuentas_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }


    /**
     * Displays a form to create a new Cupos entity.
     *
     * @Route("/new/{idCupoAnual}", name="cupos_new")
     * @Method("GET|POST")
     * @Template()
     */
    public function newAction($idCupoAnual = null)
    {
        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {

            $em = $this->getDoctrine()->getManager("ms_haberes_web");

            //$estado = $em->getRepository('LiquidacionesCuposAnualesBundle:CupoEstados')->find(1);

            $entity = new Cupos();

            //$entity->setCupoestado($estado);
            $form   = $this->createCreateForm($entity);

            $sql = "SELECT distinct d.RegSan1 as COD_TIPO_DEPENDENCIA
                    FROM Haberes.General.HADependencias d where d.RegSan1 is not null";

            //$entities = $em->createQuery($sql)->getResult();
            $connection = $this -> getDoctrine()
                                    -> getManager("ms_haberes_web")
                                    -> getConnection();

            $entityHAL = $connection ->  prepare($sql);

            $entityHAL -> execute();


            return array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'entityHAL' => $entityHAL,
                'idCupoAnual' => $idCupoAnual,
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
     * Finds and displays a Cupos entity.
     *
     * @Route("/error/{mensaje}/{direccion}", name="error")
     * @Method("GET")
     * @Template()
     */
    public function errorAction($mensaje,$direccion)
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        return array(
            'error'     => $mensaje,
            'direccion' => $direccion,
        );
    }
    /**
     * Finds and displays a Cupos entity.
     *
     * @Route("/show/{id}", name="cupos_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((false === $securityContext->isGranted('ROLE_15')) || (false === $securityContext->isGranted('ROLE_21'))) {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $usr = $this->get('security.context')->getToken()->getUser();
        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->find($id);

        $ent_HAT = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findBy(array('refCupo' => $id));

        //if ($entity->getCupoestado()->getId() == 4) {

        //}
        $CupoEstado = $entity->getCupoestado()->getCupoEstado();
        $cuentas = "";

        foreach ($ent_HAT as $cuposHAT) {
            $cupoHT = new CuposHATiposLiquidacion();
            $cupoHT = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($cuposHAT);

            $imputacion = $cupoHT->getIdImputacionPresupuestaria();

            $ent_cuenta = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($cupoHT->getRefCuenta());
            $cuentas = $cuentas.$ent_cuenta->getCuenta()."*";

        }

        $ent_imp = $em->getRepository('LiquidacionesHaberesBundle:HAImputacionPresupuestaria')->find($cupoHT->getIdImputacionPresupuestaria());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cupos entity.');
        }


        $deleteForm = $this->createDeleteForm($id)->createView();

        $resDepe = $em->getRepository('LiquidacionesHaberesBundle:HADependencias')->find($entity->getIdDependencia());

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm,
            'Depe'        => $resDepe->getId()." - ".$resDepe->getDependencia(),
            'imputacion'  => $ent_imp->getId()." - ".$ent_imp->getProgramaDescripcion(),
            'lstCuentas'  => explode("*",$cuentas),
            'TipoDepe'    => $resDepe->getRegsan1(),
            'CupoEstado'  => $CupoEstado,
        );
    }


    /**
     * Finds and displays a Cupos entity.
     *
     * @Route("/show/{refCupo}", name="cupos_showvista")
     * @Method("GET")
     * @Template()
     */
    public function showvistaAction($refcupo)
    {

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if (false === $securityContext->isGranted('ROLE_15')) {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $usr = $this->get('security.context')->getToken()->getUser();
        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->find($refcupo);

        $ent_HAT = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findBy(array('refCupo' => $refcupo));

        $this->get("session")->getFlashBag()->clear();

        //if ($entity->getCupoestado()->getId() == 4) {

        //}
        $CupoEstado = $entity->getCupoestado()->getCupoEstado();
        $cuentas = "";

        foreach ($ent_HAT as $cuposHAT) {
            $cupoHT = new CuposHATiposLiquidacion();
            $cupoHT = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($cuposHAT);

            $imputacion = $cupoHT->getIdImputacionPresupuestaria();

            $ent_cuenta = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($cupoHT->getRefCuenta());
            $cuentas = $cuentas.$ent_cuenta->getCuenta()."*";
        }


        $ent_imp = $em->getRepository('LiquidacionesHaberesBundle:HAImputacionPresupuestaria')->find($cupoHT->getIdImputacionPresupuestaria());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cupos entity.');
        }


        $deleteForm = $this->createDeleteForm($refcupo)->createView();

        $resDepe = $em->getRepository('LiquidacionesHaberesBundle:HADependencias')->find($entity->getIdDependencia());

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm,
            'Depe'        => $resDepe->getCodigo()." - ".$resDepe->getNombre(),
            'imputacion'  => $ent_imp->getId()." - ".$ent_imp->getProgramaDescripcion(),
            'lstCuentas'  => explode("*",$cuentas),
            'TipoDepe'    => $resDepe->getRegsan1(),
            'CupoEstado'  => $CupoEstado,
        );
    }


    /**
     * Displays a form to edit an existing Cupos entity.
     *
     * @Route("/{id}/edit", name="cupos_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {


            $em = $this->getDoctrine()->getManager("ms_haberes_web");

            $usr = $this->get('security.context')->getToken()->getUser();
            $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->find($id);
            $ent_HAT = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findBy(array('refCupo' => $id));


            $arEstadosNoPermitidosModificar = array("Cerrado Liquidado");
            if (in_array($entity->getCupoestado()->getCupoEstado(), $arEstadosNoPermitidosModificar)) {


                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'No se puede modificar el Cupo cuando el estado actual es "Cerrado Liquidado"'
                );
                return $this->redirect($this->generateUrl('cupos'));
            }

            $cuentas = "";

            foreach ($ent_HAT as $cuposHAT) {
                $cupoHT = new CuposHATiposLiquidacion();
                $cupoHT = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($cuposHAT);

                $imputacion = $cupoHT->getIdImputacionPresupuestaria();

                $ent_cuenta = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($cupoHT->getRefCuenta());
                $cuentas = $cuentas.$ent_cuenta->getCuenta()."*";
            }

            $ent_imp = $em->getRepository('LiquidacionesHaberesBundle:HAImputacionPresupuestaria')->find($cupoHT->getIdImputacionPresupuestaria());

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Cupos entity.');
            }

            $editForm = $this->createEditForm($entity);
            $deleteForm = $this->createDeleteForm($id);

            $depenEntity = $em->getRepository('LiquidacionesHaberesBundle:HADependencias')->find($entity->getIdDependencia());

            return array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'Depe'        => $depenEntity->getDependencia(),
                'imputacion'  => $ent_imp->getId()." - ".$ent_imp->getProgramaDescripcion(),
                'lstCuentas'  => explode("*",$cuentas),
                'TipoDepe'    => $depenEntity->getRegsan1(),
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
     * Displays a form to edit an existing Cupos entity.
     *
     * @Route("/{refCupo}/edit", name="cupos_editvista")
     * @Method("GET")
     * @Template()
     */
    public function editvistaAction($refCupo)
    {

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {

            $em = $this->getDoctrine()->getManager("ms_haberes_web");

            $usr = $this->get('security.context')->getToken()->getUser();
            $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->find($refCupo);


            $arEstadosNoPermitidosModificar = array("Cerrado Liquidado");
            if (in_array($entity->getCupoestado()->getCupoEstado(), $arEstadosNoPermitidosModificar)) {


                $request->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'No se puede modificar el Cupo cuando el estado actual es "Cerrado Liquidado"'
                );
                return $this->redirect($this->generateUrl('cupos', array('id' => $id)));
            }

            $ent_HAT = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findBy(array('refCupo' => $refCupo));

            $cuentas = "";

            foreach ($ent_HAT as $cuposHAT) {
                $cupoHT = new CuposHATiposLiquidacion();
                $cupoHT = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($cuposHAT);

                $imputacion = $cupoHT->getIdImputacionPresupuestaria();

                $ent_cuenta = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($cupoHT->getRefCuenta());
                $cuentas = $cuentas.$ent_cuenta->getCuenta()."*";
            }

            $ent_imp = $em->getRepository('LiquidacionesHaberesBundle:HAImputacionPresupuestaria')->find($cupoHT->getIdImputacionPresupuestaria());

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Cupos entity.');
            }

            $editForm = $this->createEditForm($entity);
            $deleteForm = $this->createDeleteForm($refCupo);

            $depenEntity = $em->getRepository('LiquidacionesHaberesBundle:HADependencias')->find($entity->getIdDependencia());

            return array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'Depe'        => $depenEntity->getCodigo()." - ".$depenEntity->getNombre(),
                'imputacion'  => $ent_imp->getId()." - ".$ent_imp->getProgramaDescripcion(),
                'lstCuentas'  => explode("*",$cuentas),
                'TipoDepe'    => $depenEntity->getRegsan1(),
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
    * Creates a form to edit a Cupos entity.
    *
    * @param Cupos $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Cupos $entity)
    {
        if ($entity->getCupoestado()->getId() == 4) {
            $form = $this->createForm(new \Liquidaciones\CuposAnualesBundle\Form\CuposEstadosCerradoType(), $entity, array(
            'action' => $this->generateUrl('cupos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        } else {
            $form = $this->createForm(new CuposType(), $entity, array(
            'action' => $this->generateUrl('cupos_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        }



        $form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    /**
     * Edits an existing Cupos entity.
     *
     * @Route("/{id}/update", name="cupos_update")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {

            $em = $this->getDoctrine()->getManager("ms_haberes_web");

            $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->find($id);

            $montoAntiguo = $entity->getMonto();
            $estadoAnterior = $entity->getCupoestado()->getId();

            $ent_HAT = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findBy(array('refCupo' => $id));

            $cuentas = "";

            foreach ($ent_HAT as $cuposHAT) {
                $cupoHT = new CuposHATiposLiquidacion();
                $cupoHT = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($cuposHAT);

                $imputacion = $cupoHT->getIdImputacionPresupuestaria();

                $ent_cuenta = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($cupoHT->getRefCuenta());
                $cuentas = $cuentas.$ent_cuenta->getCuenta()."*";
            }


            $ent_imp = $em->getRepository('LiquidacionesHaberesBundle:HAImputacionPresupuestaria')->find($cupoHT->getIdImputacionPresupuestaria());

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Cupos entity.');
            }


            $deleteForm = $this->createDeleteForm($id);
            $editForm = $this->createForm(new \Liquidaciones\CuposAnualesBundle\Form\CuposMontoEstadoType(), $entity);
            $editForm->bind($request);

            $nuevoMontoTransformado = (float)str_replace('.','',$entity->getMonto());
            $entity->setMonto((float)str_replace(',','.',$nuevoMontoTransformado));

            if ($montoAntiguo > $entity->getMonto()) { //SI QUIERE AGREGAR MAS PLATA AL CUPO Y EL CUPO ESTA CERRADO LO DEJO, SOLO CONTROLO SI HAY MENOS PLATA QUE ESTE CERRADO
                if (($entity->getCupoestado()->getCupoEstado() == "Cerrado Aprobado") && ($montoAntiguo != $entity->getMonto())) {
                    $request->getSession()->getFlashBag()->add(
                        'aviso_error',
                        'No se puede modificar el monto cuando el estado del Cupo Mensual es "Cerrado Aprobado", solo se puede modificar el estado.'
                    );
                    return $this->redirect($this->generateUrl('cupos_edit', array('id' => $id)));
                }
            }
            //para modificar validar que el monto liquidado
            $totalCargado = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                      JOIN l.cuposhatipoliquidacion ca
                      WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $id)->getResult();

            if ($totalCargado != null) {
                $totalGastadoCupoMensual = $totalCargado[0]["total"] == 0 ? 0 : $totalCargado[0]["total"];
            } else {
                $totalGastadoCupoMensual = 0;
            }

            if (($entity->getMonto() - $totalGastadoCupoMensual) < 0) {


                $request->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El monto que intenta cargar ($'.number_format($entity->getMonto(),2,'.',',').') es menor a la suma de los montos cargados a los agentes en el cupo ($'.number_format($totalGastadoCupoMensual,2,'.',',').')'
                );
                return $this->redirect($this->generateUrl('cupos_edit', array('id' => $id)));
            }


            $total = $em->createQuery('SELECT sum(c.monto) as total FROM LiquidacionesCuposAnualesBundle:Cupos c
                      JOIN c.cuposanuales ca
                      WHERE ca.id= :idCupoAnual')->setParameter('idCupoAnual', $entity->getCuposanuales()->getId())->getResult();
            $totalCupoMensual = $total[0]["total"];

            if (($entity->getCuposanuales()->getMonto() - $totalCupoMensual - $entity->getMonto() + $montoAntiguo) < 0) {


                $request->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El monto del cupo mensual cargado no puede superar la diferencia entre el Monto del Cupo Anual y sumatoria de los cupos mensuales ya cargados. Saldo del Cupo Anual "'.$entity->getCuposanuales()->getDescripcion().'": $'.($entity->getCuposanuales()->getMonto() - $totalCupoMensual).' monto antiguo:'.$montoAntiguo
                );
                return $this->redirect($this->generateUrl('cupos_edit', array('id' => $id)));
            }


            if ($editForm->isValid()) {

                $em->persist($entity);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                    'aviso_ok',
                    'Los datos fueron guardados correctamente!'
                );

                return $this->redirect($this->generateUrl('cupos_edit', array('id' => $id)));
            }

            $request->getSession()->getFlashBag()->add(
                'aviso_error',
                'Hubo algún error verificar nuevamente!: '
            );

            $resDepe = $em->getRepository('LiquidacionesHaberesBundle:HADependencias')->find($entity->getIdDependencia());

            return array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'Depe'        => $resDepe->getId()." - ".$resDepe->getDependencia(),
                'imputacion'  => $ent_imp->getId()." - ".$ent_imp->getProgramaDescripcion(),
                'lstCuentas'  => explode("*",$cuentas),
                'TipoDepe'    => $resDepe->getRegsan1(),
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
     * Deletes a Cupos entity.
     *
     * @Route("/{id}/delete", name="cupos_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, $id)
    {

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {


                $em = $this->getDoctrine()->getManager("ms_haberes_web");
                $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->find($id);

                if (!$entity) {
                    throw $this->createNotFoundException('Unable to find Cupos entity.');
                }

                if ($entity->getCupoestado()->getId() != 1) {

                    $request->getSession()->getFlashBag()->add(
                        'aviso_error',
                        'No se puede borrar, el cupo debe estar cerrado.'
                    );
                    return $this->redirect($this->generateUrl('cupos_edit', array('id' => $id)));
                }

                //para modificar validar que el monto liquidado
                $totalCargado = $em->createQuery('SELECT sum(l.montoTotalCalculado) as total FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                          JOIN l.cuposhatipoliquidacion ca
                          WHERE ca.refCupo= :idCupo')->setParameter('idCupo', $id)->getResult();

                if ($totalCargado != null) {
                    $totalGastadoCupoMensual = $totalCargado[0]["total"] == 0 ? 0 : $totalCargado[0]["total"];
                } else {
                    $totalGastadoCupoMensual = 0;
                }

                if ($totalGastadoCupoMensual > 0) {


                    $request->getSession()->getFlashBag()->add(
                        'aviso_error',
                        'No se puede borrar el Cupo Mensual porque tiene cargado Agentes en la liquidación.'
                    );
                    return $this->redirect($this->generateUrl('cupos_edit', array('id' => $id)));
                }

                $entityHAC = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findBy(array('refCupo'=> $id));
                foreach ($entityHAC as $cuposhaliquidacion) {
                    $em->remove($cuposhaliquidacion);
                    $em->flush();
                }

                $em->remove($entity);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                    'aviso_ok',
                    'Se borro correctamente el cupo.'
                );


            return $this->redirect($this->generateUrl('cupos'));

        } else {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));


        }
    }

    /**
     * Creates a form to delete a Cupos entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     *
     * @Route("/verCuentas/", name="cuentas_verCuentas")
     */
    public function verCuentasAction()
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $consulta = $em->createQuery("SELECT c.id, reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(c.cuenta,'ú','u'),'ó','o'),'í','i'),'é','e'),'á','a'),'Ú','U'),'Í','I'),'É','E'),'Á','A'),'Ó','O'),'Ñ','N'),'ñ','n') as cuenta FROM LiquidacionesCuposAnualesBundle:Cuentas c order by c.cuenta asc");

        //$res = $consulta->getResult();

        //$response = new Response('marcos');
        //$response = new Response(json_encode(array('id'=>1, 'Cuenta'=>'Reemplazos')));
        $ar = array();

        foreach ($consulta->getResult() as $itemC) {
          array_push($ar, array('id' => $itemC['id'],'cuenta' => utf8_encode($itemC['cuenta'])));
        }
        $response = new Response(json_encode($ar));
        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');

        return $response;


        /*
        return array(
            'entities' => $consulta->getResult(),
        );
        */
        //return array('marcos' => 'marcos');
    }


    /**
     *
     * @Route("/verDependencias/{regsan}", name="dependencias_verDependencias")
     * @Method("GET")
     * @Template()
     */
    public function verDependenciasAction($regsan)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        /*
        $consulta = $em->createQuery("SELECT d.id, reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(reemplazar(d.dependencia,'ú','u'),'ó','o'),'í','i'),'é','e'),'á','a'),'Ú','U'),'Í','I'),'É','E'),'Á','A'),'Ó','O'),'Ñ','N') as dependencia, d.regsan1 as COD_TIPO_DEPENDENCIA FROM LiquidacionesHaberesBundle:HADependencias d where d.regsan1 = '".$regsan."' ORDER BY d.id ASC");
        */
        $consulta = $em->createQuery("SELECT d.id, d.dependencia, d.regsan1 as COD_TIPO_DEPENDENCIA FROM LiquidacionesHaberesBundle:HADependencias d where d.regsan1 = '".$regsan."' ORDER BY d.id ASC");


        $ar = array();

        foreach ($consulta->getResult() as $itemC) {
          array_push($ar, array('id' => $itemC['id'],'dependencia' => utf8_encode($itemC['dependencia']),'COD_TIPO_DEPENDENCIA' => utf8_encode($itemC['COD_TIPO_DEPENDENCIA']) ));
        }

        $response = new Response(json_encode($ar));

        $response->headers->set('Content-Type', 'application/json; charset=UTF-8');

        return $response;

    }



    /**
     * @Route("/cupos/cuposdatatable", name="cuposdatatable")
     *
     */
    public function tableAction() {

        $dt = new \Liquidaciones\CuposAnualesBundle\Helpers\DataTable(array( "ID", "ANIO", "MES", "NOMBRE", "MONTO", "CUENTA"));

        $dt->setQuery("select c.ID,c.Anio,c.Mes,d.Nombre,c.Monto,cc.Cuenta from LiquidacionesWeb.dbo.Cupos c "
                . " inner join TREFE14.Dependencias d on c.iddependencia = d.codigo "
                . " inner join LiquidacionesWeb.dbo.CuposHATiposLiquidacion h on c.id = h.refcupo "
                . " inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = h.refcuenta ");

        $connection = $this -> getDoctrine()
                            -> getManager("ms_haberes_web")
                            -> getConnection();

        $stmt = $connection ->  prepare($dt ->  strQuery);

        $stmt -> execute();

        $rResult = $stmt->fetchAll(PDO::FETCH_ASSOC );

        if (count($rResult) > 0){
            $arrResult = $rResult[0]["TOTAL"];
        } else {
            $arrResult = 0;
        }

        return new Response($dt->buildDataTable($rResult, $arrResult, count($rResult)));
    }


    /**
     * @Route("/spAgregaPeriodo/{anio}/{mes}/{mesactual}/{icupoanual}/{nuevocupoanual}/{usuario}/{todos}/", name="spAgregaPeriodo")
     * @Template()
     */
    public function spAgregaPeriodoAction($anio,$mes,$mesactual,$icupoanual,$nuevocupoanual,$usuario,$todos) {

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {


            $em = $this->getDoctrine()->getManager("ms_haberes_web");

            $cantidad = 0;
            $contador='BEGIN LIQUIDACIONES.PKG_INSERTPERIODO.inserta_periodo(:anio, :mes, :mesactual, :icupoanual, :nuevocupoanual, :usuario, :todos); END;';
            //$contador='SELECT CANTIDADCUPOSANUALES(:c_anio) AS cant FROM dual';
            $stmt = $em->getConnection()
                    ->prepare($contador);
            $stmt->bindValue(':anio', $anio);
            $stmt->bindValue(':mes', $mes);
            $stmt->bindValue(':mesactual', $mesactual);
            $stmt->bindValue(':icupoanual', $icupoanual);
            $stmt->bindValue(':nuevocupoanual', $nuevocupoanual);
            $stmt->bindValue(':usuario', $usuario);
            $stmt->bindValue(':todos', $todos);
            //$stmt->bindValue(':c_cantidad', $cantidad);

            $stmt->execute();
            //$nro = $stmt->fetchAll();

            return new Response('Operación Completa!!');

        } else {

            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));


        }
    }

    /**
     * @Route("/periodo/", name="periodo")
     * @Template()
     */
    public function periodoAction() {

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {

            $anio = date('Y');
            $usr = $this->get('security.context')->getToken()->getUser();
            $sqlCA = "select ca.ID,ca.Descripcion,ca.Monto from LiquidacionesWeb.dbo.CuposAnuales ca "
                    . " where ca.anio = ".$anio;
            //$em = $this->getDoctrine()->getManager("ms_haberes_web");
            //$periodo = $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->findBy(array('anio' => 2014,'mes' => 10));

            if (date('m')== 1) {
                $anio = $anio - 1;
                $sqlCAA = "select ca.ID,ca.Descripcion,ca.Monto from LiquidacionesWeb.dbo.CuposAnuales ca "
                    . " where ca.anio = ".$anio;
            } else {
                $sqlCAA = "select ca.ID,ca.Descripcion,ca.Monto from LiquidacionesWeb.dbo.CuposAnuales ca "
                    . " where ca.anio = ".$anio;
            }

            $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();


            $resCA = $connection-> prepare($sqlCA);

            $resCA-> execute();

            $resCAA = $connection-> prepare($sqlCAA);

            $resCAA-> execute();

            return array(
                'anio' => date('Y-m-d', strtotime('-1 month')),
                'mes' => date('Y-m-d', strtotime('-1 month')),
                'usuario' => 'Marcos',
                'entitiesca' => $resCA,
                'entitiesanterior' => $resCAA,
            );

        } else {

            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));

        }
    }


    public function traerImputacion($depe,$cuenta,$tipo)
    {

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $ent_cuenta = new Cuentas();
        $ent_cuenta = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($cuenta);

        //die(var_dump($depe));

        //$codigo = $emL->getRepository('LiquidacionesReferenciasBundle:Dependencias')->find($depe)->getCodigo();
        $codigo     = $depe;

        $idTipoLiquidacion = $ent_cuenta->getIdTipoLiquidacion();

        //[Preliquidacion].[spImputacionCorrespondiente] (@IdPersonalCargo int?, @IdTipoLiquidacion int, @IdDepeForzadaHsEx int?)
        $spTraerImputaciones     = "exec haberes.Preliquidacion.spImputacionCorrespondiente null,".$idTipoLiquidacion.",".$codigo;

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
        $stmt = $conn->prepare($spTraerImputaciones);
        $stmt->execute();

        $rResult = '';

        $rResult = $stmt->fetchAll();

        //return $consulta->getSingleResult();
        return $rResult;
    }


    /**
     * @Route("/periododetalles/{cupoanual}/{cupoanualanterior}", name="cupos_periododetalles")
     * @Method("GET")
     * @Template()
     */
    public function periododetallesAction($cupoanual,$cupoanualanterior) {

        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {


            $em = $this->getDoctrine()->getManager("ms_haberes_web");
            $usr = $this->get('security.context')->getToken()->getUser();
            $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposAnuales')->find($cupoanual);

            $montoAnual = $entity->getMonto();

            $mes = date('m');
            $anio = date('Y');

            if ($mes == 1) {
                if ($cupoanualanterior == 0) {
                    $error = 'Debe seleccionar un Cupa Anual Anterior.';
                    $direccion = "periodo";
                    return $this->redirect($this->generateUrl('error', array('mensaje' => $error,'direccion' => $direccion)));
                }

                $cupoanual = $cupoanualanterior;
                $mes = 12;
                $anio = $anio - 1;
            }

            $sqlAcumulado = "select sum(c.Monto) as Monto from LiquidacionesWeb.dbo.Cupos c "
                    . " inner join LiquidacionesWeb.dbo.CuposHATiposLiquidacion h on c.id = h.refcupo "
                    . " inner join LiquidacionesWeb.dbo.CuposAnuales ca on ca.id = c.refcupoanual "
                    . " inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = h.refcuenta "
                    . " where c.anio in (".$anio.") and c.mes <= ".$mes." and ca.ID = ".$cupoanual;
            //die(var_dump($sqlAcumulado));

            //traigo el periodo del mes anterior
            $sql = "select c.ID,c.Anio,c.Mes,d.iddependencia as codigo, d.dependencia as nombre,c.Monto,cc.Cuenta,c.refcupoanual, ca.descripcion, h.refcuenta, d.regsan1 as Cod_Tipo_Dependencia from LiquidacionesWeb.dbo.Cupos c "
                    . " inner join Haberes.General.HADependencias d on c.iddependencia = d.iddependencia "
                    . " inner join LiquidacionesWeb.dbo.CuposHATiposLiquidacion h on c.id = h.refcupo "
                    . " inner join LiquidacionesWeb.dbo.CuposAnuales ca on ca.id = c.refcupoanual "
                    . " inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = h.refcuenta "
                    . "left join (select d.iddependencia as codigonuevo  from LiquidacionesWeb.dbo.Cupos c
                        inner join LiquidacionesWeb.dbo.CuposHATiposLiquidacion h on c.id = h.refcupo
                        inner join LiquidacionesWeb.dbo.CuposAnuales ca on ca.id = c.refcupoanual
                        inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = h.refcuenta
                        inner join Haberes.General.HADependencias d on c.iddependencia = d.iddependencia
                        where c.anio = ".$anio." and c.mes = ".$mes." and ca.ID = ".$cupoanual." and h.adicional = 0) m
                        on          m.codigonuevo = d.iddependencia"
                    . " where c.anio = ".$anio." and c.mes = ".(((integer)$mes)-1)." and ca.ID = ".$cupoanual." and m.codigonuevo is null";

            //obtengo
            $sqlTL = "select cu.idTipoLiquidacion "
                    ." FROM        LiquidacionesCuposAnualesBundle:Cupos c "
                    ." INNER JOIN "
                    ." LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion im "
                    ." WITH          c.id = im.refCupo "
                    ." INNER JOIN "
                    ." LiquidacionesCuposAnualesBundle:Cuentas cu "
                    ." WITH          im.refCuenta = cu.id ";
            $sqlTL = $sqlTL." WHERE c.anio = ".$anio." and c.mes = ".(((integer)$mes)-1)." and c.refCupoAnual = ".$cupoanual;
            $sqlTL = $sqlTL." Group by cu.idTipoLiquidacion ";

            //die(var_dump($sqlTL));

            $consulta = $em->createQuery($sqlTL);
            $resTL = $consulta->getResult();

            if ($resTL == null) {
                $direccion = "periodo";
                $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'No existen liquidaciones anteriores para copiar.'
                );
                return $this->redirect($this->generateUrl($direccion));
            }

            $TL = $resTL[0]["idTipoLiquidacion"];

            //falta la parte de buscar las imputaciones quew faltan

            $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

            $stmt = $connection ->  prepare($sql);

            $stmt -> execute();

            $stmtAcu = $connection ->  prepare($sqlAcumulado);

            $stmtAcu -> execute();
            /*
            $lstImpFaltantes = $connection ->  prepare($impuFaltantes);

            $lstImpFaltantes -> execute();
            */
            if (date('m') == 1) {
                $todos = 0;
            } else {
                $todos = 1;
            }

            $montoAcumulado=0;

            return array(
                'entities'      => $stmt,
                'anio' => date('Y'),
                'mes' => date('m'),
                'usuario' => 'Marcos',
                'todos' => $todos,
                'montoAnual'=>$montoAnual,
                'montoAcumulado' => $stmtAcu,
                'refcupoanual' => $cupoanual,
                'cupoanterior'=>$cupoanualanterior,
                //'lstImputacionesFaltantes' => $lstImpFaltantes,
            );

        } else {

            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));

        }
    }


    //cargarperiododetalles

    /**
     * Creates a new Cupos entity.
     *
     * @Route("/cargarperiododetalles", name="cupos_cargarperiododetalles")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:cargarperiododetalles.html.twig")
     */
    public function cargarperiododetallesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entity = new Cupos();

        $usr = $this->get('security.context')->getToken()->getUser();

        //$formC = $this->createCreateFormCuentas($cuentas);
        $data = $request->request->all();
        //$name = $data['form']['name'];
        $mes = (integer)$data['mes'];
        $anio = (integer)$data['anio'];
        $refcupoanual = $data['refcupoanual'];
        $cupoestado = new \Liquidaciones\CuposAnualesBundle\Entity\CupoEstados();

        $montoTotal = 0;
        $cantidad = 0;

        $lstImputaciones = '';

        for ($i=1;$i<=$data['cantidad'];$i++) {
            //$em->getConnection()->beginTransaction();
            $depe   = $data['depe'.$i];
            //$entity->setMonto((float)str_replace(',','.',$entity->getMonto()));
            $monto  = str_replace('.','',$data['monto'.$i]);
            $monto  = (float)str_replace(',','.',$monto);
            $refcuenta = $data['refcuenta'.$i];
            $tipo = $data['tipodepe'.$i];
            $imputacion = $this->traerImputacion($depe, $refcuenta, $tipo);

            //$lstImputaciones = $lstImputaciones."<br><br>".$imputacion[0]['id'];

            //var_dump($imputacion[0]['id']);

            $cupos = new Cupos();
            $cupos->setAnio($anio);
            $cupoestado = $em->getRepository('LiquidacionesCuposAnualesBundle:CupoEstados')->find(1);
            $cupos->setCupoestado($cupoestado);
            $cupos->setRefCupoEstado(1);
            $cupoestado = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposAnuales')->find($refcupoanual);
            $cupos->setCuposanuales($cupoestado);
            $refDepe = $em->getRepository('LiquidacionesHaberesBundle:HADependencias')->findBy(array('id' => $depe));


            //$cupos->setDependencias($emR->getRepository('LiquidacionesReferenciasBundle:Dependencias')->find($refDepe[0]->getId()));
            //$cupos->setDependencias(460);
            //die(var_dump($refDepe[0]->getId()));
            $cupos->setIdDependencia($refDepe[0]->getId());

            $cupos->setFechaCrea(new \DateTime());
            $cupos->setFechaModi(new \DateTime());
            $cupos->setMes($mes);
            $cupos->setMonto($monto);
            $cupos->setRefCupoAnual($refcupoanual);
            $cupos->setUsuaCrea('ssmarcos');
            $cupos->setUsuaModi('ssmarcos');

            $em->persist($cupos);
            $em->flush();

            $montoTotal = $montoTotal + $monto;
            $cantidad = $cantidad + 1;

            $cuposHATL = new CuposHATiposLiquidacion();
                $cuposHATL->setAdicional(0);
                $cuenta = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($refcuenta);
                $cuposHATL->setCuentas($cuenta);
                $cuposHATL->setIdImputacionPresupuestaria($imputacion[0]['IdImputacionPresupuestaria']);
                $cuposHATL->setRefCuenta($refcuenta);
                $cuposHATL->setCupos($cupos);
                $cuposHATL->setRefCupo($cupos->getId());
                $em->persist($cuposHATL);
                $em->flush();
        }



        //var_dump($data);
        //return $this->redirect($this->generateUrl('cupos_show', array('id' => $entity->getId())));

        return array(
            'montototal' => $montoTotal,
            'cantidad'   => $cantidad,
            'lstimputaciones' => $lstImputaciones,
        );
    }


    /**
     * Creates a new Cupos entity.
     *
     * @Route("/modificarestados", name="cupos_modificarestados")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:modificarestados.html.twig")
     */
    public function modificarestadosAction()
    {
        //Session
        $session = $this->getRequest()->getSession();

        //Seguridad
        $securityContext = $this->get('security.context');

        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {

            $em = $this->getDoctrine()->getManager("ms_haberes_web");


            $sql = "SELECT ca.id,
                                c.anio,
                                c.mes,
                                c.monto,
                                e.cupoEstado,
                                convert(varchar(5),d.iddependencia) + ' - ' + d.dependencia as codigo,
                                d.regsan1 as region,
                                cc.cuenta,
                                ca.adicional,
                                a.descripcion
                        FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                      inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                      inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                      inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                      inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                      inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                      where cc.id not in (2,3,18,19,22,23) and e.id <> 4

                    union all


            select  d.id,
                    d.anio,
                    d.mes,
                    d.monto,
                    d.cupoEstado,
                    d.codigo,
                    d.Cod_Tipo_Dependencia as region,
                    min(d.cuentaModulo) + ' / ' + min(d.cuentaBeca) as cuenta,
                    d.adicional,
                    d.descripcion

                    from (
                    SELECT ca.id,
                                c.anio,
                                c.mes,
                                c.monto,
                                e.cupoEstado,
                                convert(varchar(5),d.iddependencia) + ' - ' + d.dependencia as codigo,
                                d.regsan1 as Cod_Tipo_Dependencia,
                                cc.cuenta as cuentaModulo,
                                '' as cuentaBeca,
                                ca.adicional,
                                a.descripcion
                        FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                      inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                      inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                      inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                      inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                      inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                      where cc.id in (2,18,22) and e.id <> 4

                    union all

                    SELECT ca.id,
                                c.anio,
                                c.mes,
                                c.monto,
                                e.cupoEstado,
                                convert(varchar(5),d.iddependencia) + ' - ' + d.dependencia as codigo,
                                d.regsan1 as Cod_Tipo_Dependencia,
                                '' as cuentaModulo,
                                cc.cuenta as cuentaBeca,
                                ca.adicional,
                                a.descripcion
                        FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                      inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                      inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                      inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                      inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                      inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                      where cc.id in (3,19,23) and e.id <> 4) d
                group by d.anio,
                    d.mes,
                    d.monto,
                    d.cupoEstado,
                    d.adicional,
                    d.codigo,
                    d.descripcion,
                    d.Cod_Tipo_Dependencia
                    ,d.id";

            //$entities = $em->createQuery($sql)->getResult();
            $connection = $this -> getDoctrine()
                                    -> getManager("ms_haberes_web")
                                    -> getConnection();

            $entities2 = $connection ->  prepare($sql);

            $entities2 -> execute();


            return array(
                'entities' => $entities2,
            );

        } else {

            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));

        }
    }


    public function getUltimoDiaMes($elAnio,$elMes) {
        return date("d",(mktime(0,0,0,$elMes+1,1,$elAnio)-1));
    }
    /**
     * Creates a new Cupos entity.
     *
     * @Route("/modificarestadosmasivocerrado", name="cupos_modificarestadosmasivocerrado")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:modificarestadosmasivo.html.twig")
     */
    public function modificarestadosmasivoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $cuposHATL  = new CuposHATiposLiquidacion();
        $cupos      = new Cupos();

        $data = $request->request->all();

        $estado = (integer)$data["grid_cb17420af4f125edd4b5e3f6010ba23f"]["__action_id"];
        //die(var_dump($data["grid_cb17420af4f125edd4b5e3f6010ba23f"]["__action"]));
        if ($estado != 4) {
            foreach ($data["grid_cb17420af4f125edd4b5e3f6010ba23f"]["__action"] as $clave => $value) {
                //die(var_dump($clave));
                $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($clave);
                //die(var_dump($cuposHATL));
                $cupos      =  $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->find($cuposHATL->getCupos()->getId());

                $cupos->setRefCupoEstado($estado);
                $em->persist($cupos);
                $em->flush();
            }
        } else {
            foreach ($data["grid_cb17420af4f125edd4b5e3f6010ba23f"]["__action"] as $value) {

                ///////////// Creo las cabeceras si existen   //////////////////////////////////////
                $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($clave);
                $cupos      = $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->find($cuposHATL->getCupos()->getId());
                $cupos->setRefCupoEstado($estado);
                $em->persist($cupos);

                $vigenciaDesde    = $cupos->getAnio().'-'.$cupos->getMes().'-01';
                $vigenciaHasta    = $cupos->getAnio().'-'.$cupos->getMes().'-'.$this->getUltimoDiaMes($cupos->getAnio(),$cupos->getMes());

                /////////////             FIN                 //////////////////////////////////////


                /////////////   Creo los cargos   //////////////////////////////////////

                $sql =       "select l.idPersonalCargo,
                                     d.iddependencia as codigo,
                                     ca.idImputacionPresupuestaria "
                        ." FROM        LiquidacionesCuposAnualesBundle:Liquidaciones l "
                        ." INNER JOIN "
                        ." LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion ca "
                        ." WITH          ca.id = l.refCupoTipoLiquidacion "
                        ." INNER JOIN "
                        ." LiquidacionesCuposAnualesBundle:Cupos c "
                        ." WITH          c.id = ca.refCupo "
                        ." INNER JOIN "
                        ." LiquidacionesHaberesBundle:HADependencias d "
                        ." WITH          d.iddependencia = c.idDependencia "
                        ." WHERE ca.id = ".$clave."  "
                        ." GROUP BY l.idPersonalCargo, d.iddependencia,ca.idImputacionPresupuestaria ";

                $agentesC = $em->createQuery($sql)->getResult();

                foreach ($agentesC as $itemC) {

                    //inserto el cargo y me retorna el id nuevo

                    $sql = 'EXEC haberes.dbo.spInsertaCargoSimple '.$itemC['idPersonalCargo'].','.(integer)$itemC['codigo'].',956,'.$itemC['idImputacionPresupuestaria'];

                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();

                    $idCargo = $rResult[0]['cargo'];
                    /////////////   Creo los Detalles   //////////////////////////////////////
                    $agentesD = $em->createQuery('SELECT l.idPersonalCargo,
                                            l.idConcepto,
                                            COALESCE(sum(l.hsExValorHora),0) as hsexvalorhora,
                                            COALESCE(sum(l.hsExCantSimples),0) as hsexcantsimples,
                                            COALESCE(sum(l.hsExCantDobles),0) as hsexcantdobles,
                                            COALESCE(sum(l.montoTotalCalculado),0) as montototalcalculado
                                            FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                                        JOIN l.cuposhatipoliquidacion ca
                                        WHERE ca.id= :idCupoL and l.idPersonalCargo= :idPersonalCargo
                                        GROUP BY l.idPersonalCargo, l.idConcepto')->setParameters(array('idCupoL'=> $clave,'idPersonalCargo'=>$itemC['idPersonalCargo']))->getResult();

                    //die(var_dump($agentesD));

                    foreach ($agentesD as $itemD) {

                        $sql = "EXEC haberes.dbo.spInsertarDetallesSimple ".$idCargo.",".$itemD['idConcepto'].",".$itemD['hsexcantsimples'].",".$itemD['hsexcantdobles'].",".$itemD['montototalcalculado'].",".$itemD['hsexvalorhora'].",'".$vigenciaDesde."','".$vigenciaHasta."','Horas Extras' ";

                        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                        $stmt = $conn->prepare($sql);

                        $stmt->execute();

                    }

                    /////////////             FIN                 //////////////////////////////////////
                }


                /////////////             FIN                 //////////////////////////////////////


            }
        }



        $direccion = "cupos_modificarestados";
        return $this->redirect($this->generateUrl($direccion));

    }


    /**
     * Creates a new Cupos entity.
     *
     * @Route("/rptcuposporcuentas", name="rptcuposporcuentas")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:index.html.twig")
     */
    public function rptcuposporcuentasAction(Request $request)
    {

        //Seguridad
        $securityContext = $this->get('security.context');

        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        if (($securityContext->isGranted('ROLE_16')) || ($securityContext->isGranted('ROLE_17'))) {
            $puedeModificar = 1;
        } else {
            $puedeModificar = 0;
        }

        if ($securityContext->isGranted('ROLE_19')) {
            $liquida = 1;
        } else {
            $liquida = 0;
        }

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entity = new Cupos();

        //  var_dump($data);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $formC   = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findBy(array('esPresupuestaria'=>'0'));
        $formCP   = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findBy(array('esPresupuestaria'=>'1'));


        $sql = "SELECT distinct d.RegSan1 as COD_TIPO_DEPENDENCIA
                    FROM Haberes.General.HADependencias d where d.RegSan1 is not null";

        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $entityHAL = $connection ->  prepare($sql);

        $entityHAL -> execute();
        //$formC = $this->createCreateFormCuentas($cuentas);
        $data = $request->request->all();



        //die(var_dump($data["btnradioA"]));

        $idCuenta       =   $data["refcuentas"];
$anioR          =   $data["anio"];
        $mesR           =   $data["mes"];

        $sql =       "select cc.id as idcuenta,cc.cuenta,a.descripcion"
                        ." FROM        LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion ca "
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
                        ." WHERE cc.id = ".$idCuenta." and c.anio = ".$anioR." and c.mes = ".$mesR." GROUP BY cc.id,cc.cuenta,a.descripcion";


        $resCuentas = $em->createQuery($sql)->getResult();


        $sql =       "select d.dependencia as codigo, c.mes, c.anio, e.cupoEstado, "
                        . " d.id,"
                        . " d.regsan1 as region,"
                        . " sum(c.monto) as total, fc.fechaDesde, fc.fechaHasta "
                        ." FROM        LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion ca "
                        ." INNER JOIN "
                        ." LiquidacionesCuposAnualesBundle:Cupos c "
                        ." WITH          c.id = ca.refCupo "
                        ." LEFT JOIN "
                        ." LiquidacionesCuposAnualesBundle:FechaCierre fc "
                        ." WITH          c.id = fc.refCupo "
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
                        ." WHERE cc.id = ".$idCuenta." and c.anio = ".$anioR." and c.mes = ".$mesR." GROUP BY d.id,d.dependencia, d.regsan1, c.mes, c.anio, e.cupoEstado, fc.fechaDesde, fc.fechaHasta";


        $lstAgentes = $em->createQuery($sql)->getResult();

        $cabeceras = array("Codigo",'Dependencia','Tipo Depe.','Mes','Año','Estado','Total','Fecha Desde','Fecha Hasta');
        $cabecerasDatos = array('id','codigo','region','mes','anio','cupoEstado','total','fechaDesde','fechaHasta');
        $titulo     = "Liquidación: ";
        $this->rptExcelAction($lstAgentes, $cabecerasDatos, $cabeceras, $titulo, $request);


        $sql = "SELECT c.id,
                            c.anio,
                            c.mes,
                            c.monto,
                            e.cupoEstado,
                            CONCAT(CONCAT(d.codigo,' - '),d.nombre) as codigo,
                            cc.cuenta,
                            ca.adicional,
                            a.descripcion,
                            d.regsan1 as COD_TIPO_DEPENDENCIA
                    FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                  inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                  inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                  inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                  inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                  inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                  where cc.id not in (2,3,18,19,22,23)

                union all


        select  d.id,
                d.anio,
                d.mes,
                d.monto,
                d.cupoEstado,
                d.codigo,
                min(d.cuentaModulo) + ' / ' + min(d.cuentaBeca) as cuenta,
                d.adicional,
                d.descripcion,
                d.COD_TIPO_DEPENDENCIA

                from (
                SELECT c.id,
                            c.anio,
                            c.mes,
                            c.monto,
                            e.cupoEstado,
                            convert(varchar(5),d.iddependencia) + ' - ' + d.dependencia as codigo,
                            cc.cuenta as cuentaModulo,
                            '' as cuentaBeca,
                            ca.adicional,
                            a.descripcion,
                            d.regsan1 as COD_TIPO_DEPENDENCIA
                    FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                  inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                  inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                  inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                  inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                  inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                  where cc.id in (2,18,22)

                union all

                SELECT c.id,
                            c.anio,
                            c.mes,
                            c.monto,
                            e.cupoEstado,
                            convert(varchar(5),d.iddependencia) + ' - ' + d.dependencia as codigo,
                            '' as cuentaModulo,
                            cc.cuenta as cuentaBeca,
                            ca.adicional,
                            a.descripcion,
                            d.regsan1 as COD_TIPO_DEPENDENCIA
                    FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                  inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                  inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                  inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                  inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                  inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                  where cc.id in (3,19,23)) d
            group by d.anio,
                d.mes,
                d.monto,
                d.cupoEstado,
                d.adicional,
                d.codigo,
                d.descripcion,
                d.COD_TIPO_DEPENDENCIA
                ,d.id";

        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $entities = $connection ->  prepare($sql);

        $entities -> execute();

        return array(
            'form'   => $form->createView(),
            'formCP'   => $formCP,
            'formC'   => $formC,
            'form2'   => $form->createView(),
            'form3'   => $form->createView(),
            'form4'   => $form->createView(),
            'entityHAL' => $entityHAL,
            'rescuentas'=>$resCuentas,
            'entities' => $entities,
            'usuaModifica'=>$puedeModificar,
            'puedeLiquidar'=>$liquida,
        );

    }


    /**
     * Creates a new Cupos entity.
     *
     * @Route("/reportesporcuentas", name="cupos_reportesporcuentas")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:reportesporcuentas.html.twig")
     */
    public function reportesporcuentasAction()
    {
        $session = $this->getRequest()->getSession();
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        // Creo el formulario de búsqueda
        $entity     = new Cupos();
        $cuentas    = new Cuentas();

        $form    = $this->createCreateForm($entity);
        $formC   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findBy(array('esPresupuestaria'=>'0'));
        $formCP   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findBy(array('activo'=>'1'));


        $sql = "SELECT distinct d.RegSan1 as COD_TIPO_DEPENDENCIA
                    FROM Haberes.General.HADependencias d where d.RegSan1 is not null";

        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $entityHAL = $connection ->  prepare($sql);

        $entityHAL -> execute();

        $entityHAL2 = $connection ->  prepare($sql);

        $entityHAL2 -> execute();

        return array(
            'form'   => $form->createView(),
            'formCP'   => $formCP,
            'formC'   => $formC,
            'form2'   => $form->createView(),
            'form3'   => $form->createView(),
            'form4'   => $form->createView(),
            'form5'   => $form->createView(),
            'form6'   => $form->createView(),
            'entityHAL' => $entityHAL,
            'entityHAL2' => $entityHAL2,
        );

    }


    /**
     * @Route("/reportenominaagentes", name="reportenominaagentes")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:reportesporcuentas.html.twig")
     */
    public function reportenominaagentesAction(Request $request)
    {
        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        $entity = new Cupos();

        //  var_dump($data);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        //$formC = $this->createCreateFormCuentas($cuentas);
        $data = $request->request->all();

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        //die(var_dump($data["btnradioA"]));

        $idCuenta       =   $data["cuenta"];
        $idCupoAnual    =   $data["liquidaciones_cuposanualesbundle_cupos"]["cuposanuales"];
        $Anio           =   $data["liquidaciones_cuposanualesbundle_cupos"]["Anio"];
        $Mes            =   $data["liquidaciones_cuposanualesbundle_cupos"]["Mes"];
        $region         =   $data["region"];
        $reporteTipo    =   $data["rptCuentasTipoArchivo"];



        ////// RECORDAR QUE CUENDO SE MIGRE TODO AL SQL SERVER MODIFICAR LA CONSULTA, SE CRUZA LAS PERSONAS ////////////////////////////////////
        $sql =       "select sum(l.montoTotalCalculado) as total,
                                l.idPersonalCargo as idpersonalcargo,
                                d.iddependencia as codigodepe,
                                d.dependencia,
                                l.rGCantHsGuardia as cantidad,
                                l.hsExCantSimples as simples,
                                l.hsExCantDobles as dobles,
                                pp.apellido + ' ' + pp.nombre as apyn,
                                pp.legajo,
                                ag.descripcion as agrupamiento,
                                c.mes,
                                c.anio,
                                d.RegSan1 as tipodependencia
                        FROM LiquidacionesWeb.dbo.liquidaciones l
                        INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                        INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                        INNER JOIN Haberes.Haberes.HAAgrupamiento ag ON ag.idagrupamiento = pc.IdAgrupamiento
                        INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                        INNER JOIN LiquidacionesWeb.dbo.cupos c on c.id = ca.refCupo
                        INNER JOIN Haberes.General.HADependencias d on d.iddependencia = c.idDependencia
                        INNER JOIN LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refCupoAnual
                        INNER JOIN LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refCuenta "
                        ." WHERE a.id = ".$idCupoAnual." AND c.mes = ".$Mes." AND c.anio = ".$Anio;
        if ($idCuenta != 0) {
          $sql .=          " AND cc.id = ".$idCuenta;
        }
        if ($region != 0) {
          $sql .=          " and d.regsan1 = '".$region."' ";
        }
        $sql .=          " GROUP BY l.idPersonalCargo, d.iddependencia,d.dependencia,l.rGCantHsGuardia ,l.hsExCantSimples ,l.hsExCantDobles ,pp.apellido ,pp.nombre ,pp.legajo,ag.descripcion,c.mes,c.anio,d.RegSan1"
                        ." ORDER BY d.iddependencia, pp.apellido,pp.nombre";
        //die(var_dump($sql));

        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $agentes = $connection ->  prepare($sql);
        $agentes -> execute();

        $TotalGralLiquidado = 0;

        if (count($agentes)>0) {

            $titulo = "Liquidación: ";
            $posicion = 1;

            if ($reporteTipo == 2) {
                $this->buildReport($idCupoAnual,$Anio,$Mes, $agentes, $idCuenta, $titulo, $posicion);
            }  else {
                if (($idCuenta == 22) || ($idCuenta == 23)) {
                    $cabeceras = array('Apellido Y Nombre','Legajo','Agrupamiento','Tipo de Dependencia','Cod. Depen.','Dependencia','Cantidad','Total','Año','Mes');
                    $cabecerasDatos = array('apyn','legajo','agrupamiento','tipodependencia','codigodepe','dependencia','cantidad','total','anio','mes');
                    $titulo     = "Liquidación: ";
                    $this->rptExcelAction($agentes, $cabecerasDatos, $cabeceras, $titulo, $request);
                } else {
                    $cabeceras = array('Apellido Y Nombre','Legajo','Agrupamiento','Tipo de Dependencia','Cod. Depen.','Dependencia','Cant.Simples','Cant.Dobles','Total','Año','Mes');
                    $cabecerasDatos = array('apyn','legajo','agrupamiento','tipodependencia','codigodepe','dependencia','simples','dobles','total','anio','mes');
                    $titulo     = "Liquidación: ";
                    $this->rptExcelAction($agentes, $cabecerasDatos, $cabeceras, $titulo, $request);
                }


            }

        } else {

            $this->getRequest()->getSession()->getFlashBag()->add('aviso_error', 'No hay agentes en esta Cuenta para el periodo solicitado.');
        }

        $formC   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findAll();
        $formCP   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findBy(array('esPresupuestaria'=>'1'));

        $sql = "SELECT distinct d.RegSan1 as COD_TIPO_DEPENDENCIA
                    FROM Haberes.General.HADependencias d where d.RegSan1 is not null";

        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $entityHAL = $connection ->  prepare($sql);

        $entityHAL -> execute();

        $entityHAL2 = $connection ->  prepare($sql);

        $entityHAL2 -> execute();


        return array(
            'form'   => $form->createView(),
            'formC'   => $formC,
            'formCP'   => $formCP,
            'form2'   => $form->createView(),
            'form3'   => $form->createView(),
            'form4'   => $form->createView(),
            'form5'   => $form->createView(),
            'form6'   => $form->createView(),
            'entityHAL' => $entityHAL,
            'entityHAL2' => $entityHAL2,
        );

    }


    /**
     * @Route("/reportemontospordependencias", name="reportemontospordependencias")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:reportesporcuentas.html.twig")
     */
    public function reportemontospordependenciasAction(Request $request)
    {
        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        $entity = new Cupos();

        //  var_dump($data);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        //$formC = $this->createCreateFormCuentas($cuentas);
        $data = $request->request->all();

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        //die(var_dump($data));

        $idCuenta       =   $data["cuenta"];
        $idCupoAnual    =   $data["liquidaciones_cuposanualesbundle_cupos"]["cuposanuales"];
        $Anio           =   $data["liquidaciones_cuposanualesbundle_cupos"]["Anio"];
        $Mes            =   $data["liquidaciones_cuposanualesbundle_cupos"]["Mes"];
        $region         =   $data["region"];
        $reporteTipo    =   $data["rptCuentasDependenciasTipoArchivo"];
        ////// RECORDAR QUE CUENDO SE MIGRE TODO AL SQL SERVER MODIFICAR LA CONSULTA ////////////////////////////////////

        $sql =       "select c.monto as cupo,
                          isnull(SUM(l.montoTotalCalculado),0) as gastado,
                          isnull(c.monto - SUM(l.montoTotalCalculado),0) as diferencia,
                          d.IdDependencia as codigodepe,
                          d.regsan1 as region,
                          d.dependencia as dependencia,
                          ce.cupoestado,
                          isnull(fec.fechahasta,'') as fechahasta
                        FROM          LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                        LEFT JOIN     LiquidacionesWeb.dbo.Liquidaciones l
                        on            ca.id = l.refCupoTipoLiquidacion
                        INNER JOIN    LiquidacionesWeb.dbo.cupos c
                        on            c.id = ca.refCupo
                        INNER JOIN    LiquidacionesWeb.dbo.cupoestados ce
                        on            ce.id = c.refcupoestado
                        left join LiquidacionesWeb.dbo.FechaCierre fec
                        on          fec.refCupo = c.id
                        INNER JOIN    haberes.General.hadependencias d
                        on            d.IdDependencia = c.idDependencia
                        INNER JOIN    LiquidacionesWeb.dbo.cuposanuales a
                        on            a.id = c.refCupoAnual
                        INNER JOIN    LiquidacionesWeb.dbo.Cuentas cc
                        on            cc.id = ca.refCuenta  "
                        ." WHERE a.id = ".$idCupoAnual." AND c.mes = ".$Mes." AND c.anio = ".$Anio;
                        if ($idCuenta != 0) {
                          $sql .=          " AND cc.id = ".$idCuenta;
                        }
                        if ($region != 0) {
                          $sql .=          " and d.regsan1 = '".$region."' ";
                        }
                        $sql .=          " GROUP BY d.IdDependencia, d.dependencia, c.monto,d.regsan1,ce.cupoestado,fec.fechahasta ORDER BY d.IdDependencia";
        //die(print_r($sql));
        //$agentes = $em->createQuery($sql)->getResult();

        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $agentes = $connection ->  prepare($sql);

        $agentes -> execute();

        $TotalGralLiquidado = 0;

        if (count($agentes)>0) {
            $titulo = "Montos por Dependencia: ";
            $posicion = 1;


            if ($reporteTipo == 2) {
                $this->buildReport($idCupoAnual,$Anio,$Mes, $agentes, $idCuenta, $titulo, $posicion);
            }  else {

                $cabeceras = array('Cod. Depen.','Dependencia','Grupo','Cupo','Gastado','Diferencia','Estado','Fecha Cierre');
                $cabecerasDatos = array('codigodepe','dependencia','region','cupo','gastado','diferencia','cupoestado','fechahasta');
                $this->rptExcelAction($agentes, $cabecerasDatos, $cabeceras, $titulo, $request);


            }
        } else {

            $this->getRequest()->getSession()->getFlashBag()->add('aviso_error', 'No hay agentes en esta Cuenta para el periodo solicitado.');
        }

        $formC   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findAll();
        $formCP   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findBy(array('esPresupuestaria'=>'1'));

        $sql = "SELECT distinct d.RegSan1 as COD_TIPO_DEPENDENCIA
                    FROM Haberes.General.HADependencias d where d.RegSan1 is not null";

        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $entityHAL = $connection ->  prepare($sql);

        $entityHAL -> execute();

        $entityHAL2 = $connection ->  prepare($sql);

        $entityHAL2 -> execute();

        return array(
            'form'   => $form->createView(),
            'formC'   => $formC,
            'formCP'   => $formCP,
            'form2'   => $form->createView(),
            'form3'   => $form->createView(),
            'form4'   => $form->createView(),
            'form5'   => $form->createView(),
            'form6'   => $form->createView(),
            'entityHAL' => $entityHAL,
            'entityHAL2' => $entityHAL2,
        );

    }



    /**
     * @Route("/reporteTR", name="reporteTR")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:reportesporcuentas.html.twig")
     */
    public function reporteTRAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        $entity = new Cupos();

        //  var_dump($data);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        //$formC = $this->createCreateFormCuentas($cuentas);
        $data = $request->request->all();

        //die(var_dump($data));

        $idCuenta       =   $data["cuenta"];
        $idCupoAnual    =   $data["liquidaciones_cuposanualesbundle_cupos"]["cuposanuales"];
        $Anio           =   $data["liquidaciones_cuposanualesbundle_cupos"]["Anio"];
        $Mes            =   $data["liquidaciones_cuposanualesbundle_cupos"]["Mes"];
        $mesA           =   '0'.date('m');
        $diaA           =   '0'.date('d');
        $fechaDesde     =   substr(date('Y'),-2).substr($mesA,-2).substr($diaA,-2);  //$data["liquidaciones_cuposanualesbundle_cupos"]["Desde"];
        //die(var_dump($data["fechapago"]));
        $fechaHasta     =   str_replace('-','',$data["fechapago"]);
        //$cuentaTR       =   $data["liquidaciones_cuposanualesbundle_cupos"]["CuentaTR"];
        $cuentaTR       =   'TR0006A2';
        $cbubloque2     =   '01200000146143';


        /*
                      INNER JOIN Copia.HAPersonal co ON co.RefPersonalCargo = l.idpersonalcargo
                      INNER JOIN Copia.CabeceraCopia ccc ON ccc.idcabeceracopia = co.refcabeceracopia and ccc.copia=1 and year(ccc.periodo) = ".$Anio." and month(ccc.periodo) = ".$Mes."
        */
        $sql =       "select right('0000000000' + REPLACE(convert(varchar,sum(l.montoTotalCalculado)),'.',''),10) as total,
                          max(pp.cbubloque1) as cbubloque1,
                          max(pp.cbubloque2) as cbubloque2,
                          right('000000000000000' + convert(varchar,ROW_NUMBER() OVER(ORDER BY l.idpersonalcargo DESC)),15) as referencia,
                          right('00000000000' +convert(varchar,max(pp.nrodocumento)),11) as nrodocumento,
                          replace(replace( replace( replace( replace( replace( max(pp.Apellido), 'Á', 'A' ), 'É', 'E' ), 'Í', 'I' ), 'Ó', 'O' ), 'Ú', 'U' ), 'Ñ', 'N' ) + ' ' + replace(replace( replace( replace( replace( replace( max(pp.Nombre), 'Á', 'A' ), 'É', 'E' ), 'Í', 'I' ), 'Ó', 'O' ), 'Ú', 'U' ), 'Ñ', 'N' ) as apyn,
                          l.idpersonalcargo

                      FROM LiquidacionesWeb.dbo.liquidaciones l
                      INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                      INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                      INNER JOIN Haberes.Haberes.HAAgrupamiento ag ON ag.idagrupamiento = pc.IdAgrupamiento
                      INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                      INNER JOIN LiquidacionesWeb.dbo.cupos c on c.id = ca.refCupo
                      INNER JOIN Haberes.General.HADependencias d on d.iddependencia = c.idDependencia
                      INNER JOIN LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoAnual
                      INNER JOIN LiquidacionesWeb.dbo.cuentas cc on cc.id = ca.refcuenta

                      WHERE a.id = ".$idCupoAnual." AND c.mes = ".$Mes." AND c.anio = ".$Anio." AND cc.id = ".$idCuenta."
                      group by l.idpersonalcargo
                      ORDER BY max(pp.cbubloque1),max(pp.cbubloque2)";

        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $agentes = $connection ->  prepare($sql);
        $agentes -> execute();
        //$agentes = $em->createQuery($sql)->getResult();

        $TotalGralLiquidado = 0;

        $cantidad = 0;
        $filecontent="";

        // los 1245 es la hora de generacion formato HHMM
        $hora = date('Hi');

        $bloque1 = '1'.'MTRIO DE SALUD  '.'                    '.'30626983398'.'AGUINALDOS'.$fechaDesde.$hora.$fechaHasta.$cbubloque2.'0'.$cuentaTR.'  '.'0'."\r\n";

        $bloque2 = '';

        $i = 1;
        $referencia = '000000000000000';
        if (count($agentes)>0) {

          foreach ($agentes as $item) {
            $referencia .= $i;
            $i += 1;
            $TotalGralLiquidado += (integer)$item['total'];
            $bloque2 .= '2'.$item['cbubloque1'].$item['cbubloque2'].$item['total'].substr($referencia,-15).$item['nrodocumento'].(substr($item['apyn'].'                      ',0,22)).'                  '.'0'."\r\n";
            $referencia = '000000000000000';

          }


        }

        $i += 1; // sumo el ultimo renglon del TR

        $bloque3 = '3'.str_pad($i,6,'0',STR_PAD_LEFT).str_pad($TotalGralLiquidado,11,'0',STR_PAD_LEFT).'                                                                                 0'."\r\n";


        $filecontent = $bloque1.$bloque2.$bloque3;


        $downloadfile="TR0006A2";

        header("Content-disposition: attachment; filename=$downloadfile");
        header("Content-Type: text/plain; charset=ISO-8859-1 ");
        header("Content-Type: application/force-download");
        header("Content-Transfer-Encoding: base64");
        header("Content-Length: ".strlen($filecontent));
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $filecontent;

        exit();
    }

    /**
     * @Route("/reportelistadorespaldo", name="reportelistadorespaldo")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:reportesporcuentas.html.twig")
     */
    public function reportelistadorespaldoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        $entity = new Cupos();

        //  var_dump($data);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        //$formC = $this->createCreateFormCuentas($cuentas);
        $data = $request->request->all();

        //die(var_dump($data));

        $idCuenta       =   $data["cuenta"];
        $idCupoAnual    =   $data["liquidaciones_cuposanualesbundle_cupos"]["cuposanuales"];
        $Anio           =   $data["liquidaciones_cuposanualesbundle_cupos"]["Anio"];
        $Mes            =   $data["liquidaciones_cuposanualesbundle_cupos"]["Mes"];

        ////// RECORDAR QUE CUENDO SE MIGRE TODO AL SQL SERVER MODIFICAR LA CONSULTA ////////////////////////////////////

        $sql =       "select l.montoTotalCalculado as total,
                             l.idPersonalCargo,
                             d.iddependencia as codigo,
                             d.dependencia,
                             pp.nrodocumento,
                             pp.legajo,
                             SUBSTRING(pp.CBUBloque2,3,4) as sucursal,
                             pp.apellido + ' ' + pp.nombre as apyn,
                             SUBSTRING(pp.CBUBloque2,7,6) as cuenta
                        FROM LiquidacionesWeb.dbo.liquidaciones l
                        INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                        INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                        INNER JOIN Haberes.Haberes.HAAgrupamiento ag ON ag.idagrupamiento = pc.IdAgrupamiento
                        INNER JOIN LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca ON ca.id = l.refCupoTipoLiquidacion
                        INNER JOIN LiquidacionesWeb.dbo.cupos c on c.id = ca.refCupo
                        INNER JOIN Haberes.General.HADependencias d on d.iddependencia = c.idDependencia
                        INNER JOIN LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refCupoAnual
                        INNER JOIN LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refCuenta "
                        ." WHERE a.id = ".$idCupoAnual." AND c.mes = ".$Mes." AND c.anio = ".$Anio." AND cc.id = ".$idCuenta." "
                        ." ORDER BY d.iddependencia,l.idPersonalCargo";  // por ahora pp.legajo no es un orden

        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $agentes = $connection ->  prepare($sql);
        $agentes -> execute();
        //$agentes = $em->createQuery($sql)->getResult();

        $TotalGralLiquidado = 0;

        $cantidad = 0;

        if (count($agentes)>0) {

            $titulo = "Listado de respaldo de Interdepósitos: ";
            $posicion = 1;
            $this->buildReport($idCupoAnual,$Anio,$Mes, $agentes, $idCuenta, $titulo, $posicion);
        } else {

            $this->getRequest()->getSession()->getFlashBag()->add('aviso_error', 'No hay agentes en esta Cuenta para el periodo solicitado.');
        }

        $formC   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findAll();
        $formCP   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findBy(array('esPresupuestaria'=>'1'));
        return array(
            'form'   => $form->createView(),
            'formC'   => $formC,
            'formCP'   => $formCP,
            'form2'   => $form->createView(),
            'form3'   => $form->createView(),
            'form4'   => $form->createView(),
        );

    }



    /**
     * @Route("/reporteinterdeposito", name="reporteinterdeposito")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:reportesporcuentas.html.twig")
     */
    public function reporteinterdepositoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        $entity = new Cupos();

        //  var_dump($data);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        //$formC = $this->createCreateFormCuentas($cuentas);
        $data = $request->request->all();

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        //die(var_dump($data));

        $idCuenta       =   $data["cuenta"];
        $idCupoAnual    =   $data["liquidaciones_cuposanualesbundle_cupos"]["cuposanuales"];
        $Anio           =   $data["liquidaciones_cuposanualesbundle_cupos"]["Anio"];
        $Mes            =   $data["liquidaciones_cuposanualesbundle_cupos"]["Mes"];

        ////// RECORDAR QUE CUENDO SE MIGRE TODO AL SQL SERVER MODIFICAR LA CONSULTA ////////////////////////////////////

        $sql =       "select l.montoTotalCalculado as total,
                             l.idPersonalCargo,
                             l.hsExValorHora as valorhora,
                             l.hsExCantSimples as simples,
                             l.hsExCantDobles as dobles,
                             cc.conceptoMS as concepto,
                             d.id,
                             d.dependencia "
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
                        ." WHERE a.id = ".$idCupoAnual." AND c.mes = ".$Mes." AND c.anio = ".$Anio." AND cc.id = ".$idCuenta." "
                        ." ORDER BY d.id";

        $agentes = $em->createQuery($sql)->getResult();

        $TotalGralLiquidado = 0;

        $cantidad = 0;

        if (count($agentes)>0) {

            foreach ($agentes as $item) {

                $cantidad += 1;
                $TotalGralLiquidado = $TotalGralLiquidado + $item['total'];

                //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
                $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$item['idPersonalCargo'].',5';

                $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $rResult = 0;

                $rResult = $stmt->fetchAll();

                $lstAgentesU = array(
                            'idpersonalcargo' => $item['idPersonalCargo'],
                            'apyn'=> $rResult[0]['apyn'],
                            'sucursal'=> $rResult[0]['Sucursal'],
                            'cuenta'=> $rResult[0]['Cuenta'],
                            'cuil'=> $rResult[0]['Cuil'],
                            'dv'=> $rResult[0]['dv'],
                            'dependencia'=> $item['codigo'],
                            'valorhora'=> $item['valorhora'],
                            'hssimples'=> $item['simples'],
                            'hsdobles'=> $item['dobles'],
                            'concepto'=> $item['concepto'],
                            'tipocuenta'=> $rResult[0]['TipoPago'],
                            'legajo'=> $rResult[0]['Legajo'],
                            'cantidad'=>$cantidad,
                            'total'=>$item['total'],
                            );
                $lstAgentes[] = $lstAgentesU;
            }

            $titulo = "Listado de Interdepósitos: ";
            $this->buildReport($idCupoAnual,$Anio,$Mes, $lstAgentes, $idCuenta, $titulo, 0);
        } else {

            $this->getRequest()->getSession()->getFlashBag()->add('aviso_error', 'No hay agentes en esta Cuenta para el periodo solicitado.');
        }

        $formC   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findAll();
        $formCP   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findBy(array('esPresupuestaria'=>'1'));
        return array(
            'form'   => $form->createView(),
            'formC'   => $formC,
            'formCP'   => $formCP,
            'form2'   => $form->createView(),
            'form3'   => $form->createView(),
            'form4'   => $form->createView(),
        );

    }

    /**
     * @Route("/reporteLRG", name="reporteLRG")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:reportesporcuentas.html.twig")
     */
    public function reporteLRGAction(Request $request)
    {
        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        set_time_limit(0);

        $entity = new Cupos();

        //  var_dump($data);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        //$formC = $this->createCreateFormCuentas($cuentas);
        $data = $request->request->all();

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        //die(var_dump($data));


        $Anio           =   $data["liquidaciones_cuposanualesbundle_cupos"]["Anio"];
        $Mes            =   $data["liquidaciones_cuposanualesbundle_cupos"]["Mes"];
        ////// RECORDAR QUE CUENDO SE MIGRE TODO AL SQL SERVER MODIFICAR LA CONSULTA ////////////////////////////////////

        $sql =       "exec haberes.LIQUIDACION.SpListadoRGPorMes  ".$Mes.",".$Anio;
        //die(print_r($sql));
        //$agentes = $em->createQuery($sql)->getResult();

        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $agentes = $connection ->  prepare($sql);

        $agentes -> execute();

        $TotalGralLiquidado = 0;

        if (count($agentes)>0) {
            $titulo = "Listado RG por mes ";


            $cabeceras = array('Cod. Depen.','Dependencia','Legajo','Apellido','Nombre','Legajo Reemplazo','ApyN Reemplazo','RG Fecha','Dia Semana','Concepto','Cant.','Monto');
            $cabecerasDatos = array('IdDependencia','Dependencia','Legajo','Apellido','Nombre','Legajo_Reemplazado','APEyNOM_Reemplazado','rgfecha','DiaSemana','concepto','rgcanthsguardia','montototalcalculado');
            $this->rptExcelAction($agentes, $cabecerasDatos, $cabeceras, $titulo, $request);



        } else {

            $this->getRequest()->getSession()->getFlashBag()->add('aviso_error', 'No hay agentes en esta Cuenta para el periodo solicitado.');
        }

        $formC   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findAll();
        $formCP   = $cuposHATL  = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findBy(array('esPresupuestaria'=>'1'));

        $sql = "SELECT distinct d.RegSan1 as COD_TIPO_DEPENDENCIA
                    FROM Haberes.General.HADependencias d where d.RegSan1 is not null";

        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $entityHAL = $connection ->  prepare($sql);

        $entityHAL -> execute();

        $entityHAL2 = $connection ->  prepare($sql);

        $entityHAL2 -> execute();

        return array(
            'form'   => $form->createView(),
            'formC'   => $formC,
            'formCP'   => $formCP,
            'form2'   => $form->createView(),
            'form3'   => $form->createView(),
            'form4'   => $form->createView(),
            'form5'   => $form->createView(),
            'form6'   => $form->createView(),
            'entityHAL' => $entityHAL,
            'entityHAL2' => $entityHAL2,
        );

    }



    /**
     * @Route("/reporteCuposPorPeriodos/{anio}/{mes}/{idcupoanual}/{cupoanterior}", name="reporteCuposPorPeriodos")
     * @Method("GET")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:reportesporcuentas.html.twig")
     */
    public function reporteCuposPorPeriodosAction($anio, $mes, $idcupoanual, $cupoanterior, Request $request)
    {
        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $sql = "select c.ID,c.Anio,c.Mes,d.idDependencia as Codigo, d.dependencia as Nombre,c.Monto,cc.Cuenta,c.refcupoanual, ca.descripcion, h.refcuenta, d.regsan1 as Cod_Tipo_Dependencia from LiquidacionesWeb.dbo.Cupos c "
                    . " inner join Haberes.General.HADependencias d on c.iddependencia = d.idDependencia "
                    . " inner join LiquidacionesWeb.dbo.CuposHATiposLiquidacion h on c.id = h.refcupo "
                    . " inner join LiquidacionesWeb.dbo.CuposAnuales ca on ca.id = c.refcupoanual "
                    . " inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = h.refcuenta "
                    . "left join (select d.idDependencia as codigonuevo  from LiquidacionesWeb.dbo.Cupos c
                        inner join LiquidacionesWeb.dbo.CuposHATiposLiquidacion h on c.id = h.refcupo
                        inner join LiquidacionesWeb.dbo.CuposAnuales ca on ca.id = c.refcupoanual
                        inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = h.refcuenta
                        inner join Haberes.General.HADependencias d on c.iddependencia = d.idDependencia
                        where c.anio = ".$anio." and c.mes = ".$mes." and ca.ID = ".$idcupoanual." and h.adicional = 0) m
                        on          m.codigonuevo = d.idDependencia"
                    . " where c.anio = ".$anio." and c.mes = ".(((integer)$mes)-1)." and ca.ID = ".$idcupoanual." and m.codigonuevo is null";

        $connection = $this -> getDoctrine()
                                -> getManager("ms_haberes_web")
                                -> getConnection();

        $stmt = $connection ->  prepare($sql);

        $stmt -> execute();

        $TotalGralLiquidado = 0;

        if (count($stmt)>0) {

            $cabeceras = array('Cod.Dependencia','Dependencia','Monto');
            $cabecerasDatos = array('CODIGO','NOMBRE','MONTO');
            $titulo     = "Cupos_Copiados_".date('Y-m');
            $this->rptExcelAction($stmt, $cabecerasDatos, $cabeceras, $titulo, $request);

        } else {

            $this->getRequest()->getSession()->getFlashBag()->add('aviso_error', 'No hay agentes en esta Cuenta para el periodo solicitado.');
        }

        return $this->redirect($this->generateUrl('cupos_periododetalles',array('cupoanual'=>$idcupoanual, 'cupoanualanterior'=>$cupoanterior)));


    }



    public function buildReport($idCupoAnual,$Anio,$Mes, $lstAgentes, $cuenta, $titulo, $posicion)
    {
        $pdf = $this->get("white_october.tcpdf")->create();

        //set default header data
        $PDF_HEADER_LOGO = "logo-recibo.jpg";//any image file. check correct path.
        $PDF_HEADER_LOGO_WIDTH = "40";

        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $Ecuentas = new Cuentas();

        $Ecuentas = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($cuenta);

        $PDF_HEADER_TITLE = $titulo.$Ecuentas->getCuenta();

        $PDF_HEADER_STRING = "Fecha de Proceso: ".date('Y-m-d')."\n";
        $PDF_HEADER_STRING .= "Ministerio de Salud de la Provincia de Buenos Aires"."\n";
        $PDF_HEADER_STRING .= $Ecuentas->getCuenta()."\n";

        if ($cuenta == 22) {
            //TRAIGO EL VALOR DE LA GUARDIA /////////////

            $query = $em->createQuery(
                "SELECT cc.monto
                   FROM LiquidacionesHaberesBundle:HAConceptosValor cc
                  WHERE cc.refConcepto in (514,515,516,517) and '".date('Y-m-d')."' >= cc.vigDesde and '".date('Y-m-d')."' <= cc.vigHasta
                  order by cc.refConcepto"
            );

            $conceptosValor = $query->getResult();

            $concepto24hs = $conceptosValor[0]['monto'];
            $concepto12hs = $conceptosValor[1]['monto'];
            $concepto24hsferiado = $conceptosValor[2]['monto'];
            $concepto12hsferiado = $conceptosValor[3]['monto'];
            /////////////////////////////////////////////

            $PDF_HEADER_STRING .= "12 Horas $".number_format($concepto12hs, 2, ',', '.')." - 24 Horas $".number_format($concepto24hs, 2, ',', '.')."\n";
            $PDF_HEADER_STRING .= "Feriados 12 Horas $".number_format($concepto12hsferiado, 2, ',', '.')." - 24 Horas $".number_format($concepto24hsferiado, 2, ',', '.')."\n";
        }
        if ($cuenta == 23) {
            //TRAIGO EL VALOR DE LA GUARDIA /////////////

            $query = $em->createQuery(
                "SELECT cc.monto
                   FROM LiquidacionesHaberesBundle:HAConceptosValor cc
                  WHERE cc.refConcepto in (364,365,366,367) and '".date('Y-m-d')."' >= cc.vigDesde and '".date('Y-m-d')."' <= cc.vigHasta
                  order by cc.refConcepto"
            );

            $conceptosValor = $query->getResult();

            $concepto24hs = $conceptosValor[0]['monto'];
            $concepto12hs = $conceptosValor[1]['monto'];
            $concepto24hsferiado = $conceptosValor[2]['monto'];
            $concepto12hsferiado = $conceptosValor[3]['monto'];
            /////////////////////////////////////////////
        }



        $pdf->SetHeaderData($PDF_HEADER_LOGO, $PDF_HEADER_LOGO_WIDTH, $PDF_HEADER_TITLE, $PDF_HEADER_STRING);
        $pdf->setFooterData(array(0,0,0), array(0,0,0));

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Ministerio de Salud de la Provincia de Buenos Aires');
        $pdf->SetTitle('Nomina de Agentes - Liquidados');
        $pdf->SetSubject('Nomina de Agentes - Liquidados');

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
        if ($posicion == 1) {
            $pdf->AddPage('P','A4', FALSE, FALSE);
        } else {
            $pdf->AddPage('L','A4', FALSE, FALSE);
        }
        $cant = 0;
        $pdf->SetFont('helvetica', '', 8);
        /*
        'apyn'=> $rResult[0]['apyn'],
                                'legajo'=> $rResult[0]['Legajo'],
                                'agrupamiento'=> $rResult[0]['Agrupamiento'],
                                'codigodepe'=> $item['codigo'],
                                'dependencia'=>$item['nombre'],
                                'total'=>$item['total']);


        */

        $total = 0;

        foreach ($lstAgentes as $agente) {
            $cant += 1;

            switch ($titulo) {
                case 'Liquidación: ':
                    $pdf->Cell(50, 0, utf8_encode($agente['apyn']), 'LTRB', 0, 'L', 0, '', 1);
                    $pdf->Cell(20, 0, $agente['legajo'], 'LTRB', 0, 'C', 0, '', 1);
                    $pdf->Cell(20, 0, $agente['agrupamiento'], 'LTRB', 0, 'C', 0, '', 1);
                    $pdf->Cell(10, 0, $agente['codigodepe'], 'LTRB', 0, 'C', 0, '', 0);
                    $pdf->Cell(55, 0, utf8_encode(substr($agente['dependencia'],0, 50)), 'LTRB', 0, 'L', 0, '', 1);
                    $pdf->Cell(15, 0, ($agente['cantidad'] == '' ? 'S:'.$agente['simples']." | D:".$agente['dobles'] : $agente['cantidad']), 'LTRB', 0, 'C', 0, '', 0);
                    $pdf->Cell(20, 0, "$ ".number_format($agente['total'],2,',','.'), 'LTRB', 0, 'R', 0, '', 0);


                    break;
                case 'Montos por Dependencia: ':
                    $pdf->Cell(10, 0, $agente['codigodepe'], 'LTRB', 0, 'C', 0, '', 0);
                    $pdf->Cell(105, 0, utf8_encode($agente['dependencia']), 'LTRB', 0, 'L', 0, '', 1);
                    $pdf->Cell(25, 0, "$ ".number_format($agente['cupo'],2,',','.'), 'LTRB', 0, 'R', 0, '', 0);
                    $pdf->Cell(25, 0, "$ ".number_format($agente['gastado'],2,',','.'), 'LTRB', 0, 'R', 0, '', 0);
                    $pdf->Cell(25, 0, "$ ".number_format($agente['diferencia'],2,',','.'), 'LTRB', 0, 'R', 0, '', 0);

                    break;
                case 'Listado de respaldo de Interdepósitos: ':
                    $pdf->Cell(15, 0, $agente['codigo'], 'LTRB', 0, 'C', 0, '', 1);
                    $pdf->Cell(15, 0, $agente['legajo'], 'LTRB', 0, 'C', 0, '', 1);
                    $pdf->Cell(20, 0, $agente['nrodocumento'], 'LTRB', 0, 'C', 0, '', 1);
                    $pdf->Cell(80, 0, utf8_encode(substr($agente['apyn'],0, 50)), 'LTRB', 0, 'L', 0, '', 1);
                    $pdf->Cell(25, 0, "$ ".number_format($agente['total'],2,',','.'), 'LTRB', 0, 'R', 0, '', 0);
                    $pdf->Cell(15, 0, $agente['sucursal'], 'LTRB', 0, 'C', 0, '', 1);
                    $pdf->Cell(17, 0, $agente['cuenta'], 'LTRB', 0, 'C', 0, '', 1);

                    break;
                case 'Listado de Interdepósitos: ':
                    $pdf->Cell(17, 6, $agente['legajo'], 'LTRB', 0, 'C', 0, '', 1);
                    $pdf->Cell(17, 6, $agente['cuenta'], 'LTRB', 0, 'C', 0, '', 1);
                    $pdf->Cell(8, 6, $agente['dv'], 'LTRB', 0, 'C', 0, '', 1);
                    $pdf->Cell(16, 6, $agente['sucursal'], 'LTRB', 0, 'C', 0, '', 0);
                    $pdf->Cell(12, 6, $agente['tipocuenta'], 'LTRB', 0, 'C', 0, '', 0);
                    $pdf->Cell(75, 6, utf8_encode(substr($agente['apyn'],0, 40)), 'LTRB', 0, 'L', 0, '', 1);
                    $pdf->Cell(14, 6, $agente['concepto'], 'LTRB', 0, 'C', 0, '', 0);
                    $pdf->Cell(19, 6, $agente['cuil'], 'LTRB', 0, 'C', 0, '', 0);
                    $pdf->Cell(16, 6, "$ ".number_format($agente['valorhora'],2,',','.'), 'LTRB', 0, 'C', 0, '', 0);
                    $pdf->Cell(16, 6, "$ ".number_format($agente['hssimples'],2,',','.'), 'LTRB', 0, 'C', 0, '', 0);
                    $pdf->Cell(16, 6, "$ ".number_format($agente['hsdobles'],2,',','.'), 'LTRB', 0, 'C', 0, '', 0);
                    $pdf->Cell(25, 6, "$ ".number_format($agente['total'],2,',','.'), 'LTRB', 0, 'R', 0, '', 0);
                    $pdf->Cell(28, 6, '.......................', 'LTRB', 0, 'C', 0, '', 1);

                    break;
                default:
                    break;
            }

            $total += $agente['total'];

            $pdf->Ln();

            if ($posicion == 1) {
                if ($cant == 33) {
                    $pdf->AddPage('P','A4', FALSE, FALSE);
                    $pdf->Line(10,280,200,280);
                    $cant = 0;
                }
            } else {
                if ($cant == 20) {
                    $pdf->AddPage('P','A4', FALSE, FALSE);
                    $pdf->Line(10,280,200,280);
                    $cant = 0;
                }
            }
        }

        if ($posicion == 1) {
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Line(10,250,200,250);
            $pdf->SetXY(10,255);
            $pdf->Cell(30, 0, 'Total general: ', 'B', 0, 'L', 0, '', 1);
            $pdf->Cell(10, 0, $cant, 'B', 0, 'R', 0, '', 1);

            $pdf->SetXY(150,255);
            $pdf->Cell(30, 0, "$ ".number_format($total,2,',','.'), 'B', 0, 'R', 0, '', 1);
            $pdf->Line(10,280,200,280);
        } else {
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Line(10,184,260,184);
            $pdf->SetXY(10,188);
            $pdf->Cell(30, 0, 'Total general: ', 'B', 0, 'L', 0, '', 1);
            $pdf->Cell(10, 0, $cant, 'B', 0, 'R', 0, '', 1);

            $pdf->SetXY(192,188);
            $pdf->Cell(30, 0, "$ ".number_format($total,2,',','.'), 'B', 0, 'R', 0, '', 1);
            $pdf->Line(10,207,260,207);
        }


        // ---------------------------------------------------------

        $response = new Response($pdf->Output('Liqui_'.date('m-Y').' '.$Ecuentas->getCuenta().'.pdf',"D"));

        $response->headers->set('Content-Type', 'application/pdf');

        // Send headers before outputting anything
        $response->sendHeaders();

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

        $letras = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

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
                $excel->setActiveSheetIndex(0)->setCellValue($letras[$i].$index, $agente[$cabecerasDatos[$i]]);
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
     * @Route("/exportarExcel/{refCupo}", name="cupos_exportarExcel")
     */
    public function exportarExcelAction($refCupo, Request $request)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $entity = new Liquidaciones();

        $cupoHA = new \Liquidaciones\CuposAnualesBundle\Entity\CuposHATiposLiquidacion();

        $TotalGralLiquidado = 0;

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findBy(array("refCupo"=>$refCupo));
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


        foreach ($entities as $cupo) {
            $cuenta = $cupo->getCuentas()->getModoCarga();
            $HAcupo = $cupo->getId();
            $nombrecuenta = $cupo->getCuentas()->getCuenta();
            $idtipoliquidacion = $cupo->getCuentas()->getIdTipoLiquidacion();
            $anio = $cupo->getCupos()->getAnio();
            $mes = $cupo->getCupos()->getMes();
            $adicional = $cupo->getAdicional();
        }

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
                                    0 as requireautorizacion,
                                    l.requiereAutorizacion,
                                    l.usuaAutoriza,
                                    l.id
                             FROM LiquidacionesWeb.dbo.liquidaciones l
                  INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                  INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                  INNER JOIN Haberes.Haberes.HAConceptos co ON co.IdConcepto = l.idConcepto
                  LEFT JOIN Haberes.Personal.HAPersonalCargos pcR ON pcR.idPersonalCargo = l.rGIdPersonalCargo
                  LEFT JOIN Haberes.Personal.HAPersonal ppR ON ppR.IdPersona = pcR.IdPersona
                  WHERE ca.refCupo= ".$refCupo;


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

                $excel->setActiveSheetIndex(0)->setCellValue("A1", "Apellido y Nombre");
                $excel->setActiveSheetIndex(0)->setCellValue("B1", "Legajo");
                $excel->setActiveSheetIndex(0)->setCellValue("C1", "Guardia");
                $excel->setActiveSheetIndex(0)->setCellValue("D1", "Reemplazado");
                $excel->setActiveSheetIndex(0)->setCellValue("E1", "Cantidad");
                $excel->setActiveSheetIndex(0)->setCellValue("F1", "Importe");

                // Add some data
                foreach($rResult as $agente)
                {
                    $excel->setActiveSheetIndex(0)->setCellValue("A".$index, utf8_encode($agente['apyn']));
                    $excel->setActiveSheetIndex(0)->setCellValue("B".$index, $agente['legajo']);
                    $excel->setActiveSheetIndex(0)->setCellValue("C".$index, $agente['concepto']);
                    $excel->setActiveSheetIndex(0)->setCellValue("D".$index, $agente['reemplazado']);
                    $excel->setActiveSheetIndex(0)->setCellValue("E".$index, $agente['cantidad']);
                    $excel->setActiveSheetIndex(0)->setCellValue("F".$index, $agente['total']);
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
                  INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                  INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                  INNER JOIN Haberes.Haberes.HAConceptos co ON co.IdConcepto = l.idConcepto
                  WHERE ca.refCupo= ".$refCupo;

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
                  INNER JOIN Haberes.Personal.HAPersonalCargos pc ON pc.idPersonalCargo = l.idPersonalCargo
                  INNER JOIN Haberes.Personal.HAPersonal pp ON pp.IdPersona = pc.IdPersona
                  INNER JOIN Haberes.Haberes.HAConceptos co ON co.IdConcepto = l.idConcepto
                  WHERE ca.refCupo= ".$refCupo;

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

    public function devolverNomina3ro($refCupo) {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $entity = new Liquidaciones();

        $TotalGralLiquidado = 0;

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findBy(array("refCupo"=>$refCupo));

        foreach ($entities as $cupo) {
            $cuenta = $cupo->getCuentas()->getModoCarga();
            $HAcupo = $cupo->getId();
            $nombrecuenta = $cupo->getCuentas()->getCuenta();
            $idtipoliquidacion = $cupo->getCuentas()->getIdTipoLiquidacion();
            $anio = $cupo->getCupos()->getAnio();
            $mes = $cupo->getCupos()->getMes();
            $adicional = $cupo->getAdicional();
            $idCupoAnual = $cupo->getCupos()->getCuposanuales()->getId();
        }


        $sql =       "select l.montoTotalCalculado as total,
                             l.idConcepto,
                             l.idPersonalCargo,
                             l.idConcepto,
                             l.rGIdPersonalCargo,
                             l.rGFecha,
                             d.id as codigo,
                             l.rGCantHsGuardia as cantidad,
                             l.id,
                             l.requiereAutorizacion,
                             l.usuaAutoriza "
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
                        ." WHERE a.id = ".$idCupoAnual." AND c.mes = ".$mes." AND c.anio = ".$anio." AND cc.id = ".$idCuenta." ";

        $agentes = $em->createQuery($sql)->getResult();

        foreach ($agentes as $item) {
            //Vuelvo a buscar a la persona para trearme los datos de esta/////////////////
            $sql = 'EXEC haberes.haberes.spBuscarPersonaPorLegajoLiquidaciones '.$item['idPersonalCargo'].',5';
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

            $TotalGralLiquidado = $TotalGralLiquidado + $item['total'];

            $lstAgentesU = array('id' => $item['id'],
                            'idpersonalcargo' => $item['idPersonalCargo'],
                            'apyn'=> $rResult[0]['apyn'],
                            'legajo'=> $rResult[0]['Legajo'],
                            'sucursal'=> $rResult[0]['Sucursal'],
                            'cuenta'=> $rResult[0]['Cuenta'],
                            'dv'=> $rResult[0]['dv'],
                            'dependencia'=> $item['codigo'],
                            'cantidad'=>$item['cantidad'],
                            'total'=>$item['total'],

                            'requireautorizacion'=>$item['requiereAutorizacion'],
                            'usuarioautoriza'=>$item['usuaAutoriza']);
            $lstAgentes[] = $lstAgentesU;
        }

        return $lstAgentes;
    }


    /**
     * @Route("/pasaraliquidacion/", name="cupos_pasaraliquidacion")
     * @Template()
     */
    public function pasaraliquidacionAction(Request $request) {

        $data = $request->request->all();

        $cupos = new Cupos();

        $cupoR = (integer)$data["cupo"];
        //die(var_dump($cupo));
        $cabecera = (integer)$data["cabeceras"];

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findBy(array("refCupo"=>$cupoR));
        foreach ($entities as $cupo) {

            $nombre             = $cupo->getCuentas()->getCuenta();
            $anio               = $cupo->getCupos()->getAnio();
            $mes                = $cupo->getCupos()->getMes();
            $adicional          = $cupo->getAdicional();
            $idtipoliquidacion  = $cupo->getCuentas()->getIdTipoLiquidacion();
            $id                 = $cupo->getId();
            $idCuenta           = $cupo->getCuentas()->getId();
        }

		if ($idCuenta == 25) {
          $idCuenta = 24;
        }

        if ($idCuenta == 24) {
          $ajuste = 1;
        } else {
          $ajuste = 0;
        }




        $fechaAjuste = '';
        $fechaAjusteHasta = '';

        ///////////// Creo las cabeceras si existen   //////////////////////////////////////
        $cupos      = $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->find($cupoR);
        $estados    = $em->getRepository('LiquidacionesCuposAnualesBundle:CupoEstados')->find(4);
        //die(var_dump($cupos));
        $cupos->setCupoestado($estados);
        $em->persist($cupos);

        $anioV = 0;
        $mesV = 0;
        if ($mes == 12) {
          $anioV = $anio + 1;
          $mesV = 1;
        } else {
          $mesV = $mes + 1;
          $anioV = $anio;
        }

        $vigenciaDesde    = $anioV.'-'.substr('0'.$mesV,-2).'-01';
        $vigenciaHasta    = $anioV.'-'.substr('0'.$mesV,-2).'-'.$this->getUltimoDiaMes($anioV,$mesV);

        /////////////             FIN                 //////////////////////////////////////


        /////////////   Creo los cargos   //////////////////////////////////////
        if ($idCuenta == 19) {
          $idCuenta = 18;
        }

        if (($idCuenta == 22) || ($idCuenta == 23)) {
          $sql =       "select l.idPersonalCargo,
                             d.id as codigo,
                             ca.idImputacionPresupuestaria,
                             ca.id "
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
                ." WHERE c.anio = ".$anio." and c.mes = ".$mes." and ca.refCuenta in (22,23) "
                ." GROUP BY l.idPersonalCargo, d.id,ca.idImputacionPresupuestaria, ca.id ";
        } else {
          $sql =       "select l.idPersonalCargo,
                             d.id as codigo,
                             ca.idImputacionPresupuestaria,
                             ca.id "
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
                ." WHERE c.anio = ".$anio." and c.mes = ".$mes." and ca.refCuenta = ".$idCuenta
                ." GROUP BY l.idPersonalCargo, d.id,ca.idImputacionPresupuestaria, ca.id ";
        }
        $agentesC = $em->createQuery($sql)->getResult();

        switch ($idtipoliquidacion) {
            case 39:
                foreach ($agentesC as $itemC) {

                    //inserto el cargo y me retorna el id nuevo

                    $sql = 'EXEC haberes.dbo.spInsertaCargoSimple '.$itemC['idPersonalCargo'].','.(integer)$itemC['codigo'].','.$cabecera.','.$itemC['idImputacionPresupuestaria'];

                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();

                    $idCargo = $rResult[0]['cargo'];
                    /////////////   Creo los Detalles   //////////////////////////////////////
                    $agentesD = $em->createQuery('SELECT l.idPersonalCargo,
                                            l.idConcepto,
                                            COALESCE(sum(l.hsExValorHora),0) as hsexvalorhora,
                                            COALESCE(sum(l.hsExCantSimples),0) as hsexcantsimples,
                                            COALESCE(sum(l.hsExCantDobles),0) as hsexcantdobles,
                                            COALESCE(sum(l.montoTotalCalculado),0) as montototalcalculado
                                            FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                                        JOIN l.cuposhatipoliquidacion ca
                                        WHERE ca.id= :idCupoL and ca.refCuenta= :refCuenta and l.idPersonalCargo= :idPersonalCargo
                                        GROUP BY l.idPersonalCargo, l.idConcepto')->setParameters(array('idCupoL'=> $itemC['id'],'idPersonalCargo'=>$itemC['idPersonalCargo'],'refCuenta'=>$idCuenta))->getResult();

                    //die(var_dump($agentesD));

                    foreach ($agentesD as $itemD) {

                        $sql = '';

                        if ($itemD['hsexcantsimples'] > 0) {
                          $sql = "EXEC haberes.dbo.spInsertarDetallesSimple ".$idCargo.",40,".$itemD['hsexcantsimples'].",0,".($itemD['hsexcantsimples'] * $itemD['hsexvalorhora'] * 1.5).",".$itemD['hsexvalorhora'].",'".$vigenciaDesde."','".$vigenciaHasta."','Horas Extras',".$ajuste.",'',''";
                        }

                        if ($itemD['hsexcantdobles'] > 0) {
                          $sql .= " EXEC haberes.dbo.spInsertarDetallesSimple ".$idCargo.",549,".$itemD['hsexcantdobles'].",0,".($itemD['hsexcantdobles'] * $itemD['hsexvalorhora'] * 2).",".$itemD['hsexvalorhora'].",'".$vigenciaDesde."','".$vigenciaHasta."','Horas Extras',".$ajuste.",'',''";
                        }


                        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                        $stmt = $conn->prepare($sql);

                        $stmt->execute();

                    }

                    /////////////             FIN                 //////////////////////////////////////
                }

                break;
			case 36:
                foreach ($agentesC as $itemC) {

                    //inserto el cargo y me retorna el id nuevo

                    $sql = 'EXEC haberes.dbo.spInsertaCargoSimple '.$itemC['idPersonalCargo'].','.(integer)$itemC['codigo'].','.$cabecera.','.$itemC['idImputacionPresupuestaria'];

                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();

                    $idCargo = $rResult[0]['cargo'];
                    /////////////   Creo los Detalles   //////////////////////////////////////
                    $agentesD = $em->createQuery('SELECT l.idPersonalCargo,
                                            l.idConcepto,
                                            COALESCE(sum(l.hsExValorHora),0) as hsexvalorhora,
                                            COALESCE(sum(l.hsExCantSimples),0) as hsexcantsimples,
                                            COALESCE(sum(l.hsExCantDobles),0) as hsexcantdobles,
                                            COALESCE(sum(l.montoTotalCalculado),0) as montototalcalculado
                                            FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                                        JOIN l.cuposhatipoliquidacion ca
                                        WHERE ca.id= :idCupoL and ca.refCuenta= :refCuenta and l.idPersonalCargo= :idPersonalCargo
                                        GROUP BY l.idPersonalCargo, l.idConcepto')->setParameters(array('idCupoL'=> $itemC['id'],'idPersonalCargo'=>$itemC['idPersonalCargo'],'refCuenta'=>$idCuenta))->getResult();

                    //die(var_dump($agentesD));

                    foreach ($agentesD as $itemD) {

                        $sql = '';

                        if ($itemD['hsexcantsimples'] > 0) {
                          $sql = "EXEC haberes.dbo.spInsertarDetallesSimple ".$idCargo.",462,".$itemD['hsexcantsimples'].",0,".($itemD['hsexcantsimples'] * $itemD['hsexvalorhora']).",".$itemD['hsexvalorhora'].",'".$vigenciaDesde."','".$vigenciaHasta."','Modulos SAT',".$ajuste.",'',''";
                        }



                        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                        $stmt = $conn->prepare($sql);

                        $stmt->execute();

                    }

                    /////////////             FIN                 //////////////////////////////////////
                }

                break;
            case 40:
                foreach ($agentesC as $itemC) {

                    //inserto el cargo y me retorna el id nuevo

                    $sql = 'EXEC haberes.dbo.spInsertaCargoSimple '.$itemC['idPersonalCargo'].','.(integer)$itemC['codigo'].','.$cabecera.','.$itemC['idImputacionPresupuestaria'];

                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();

                    $idCargo = $rResult[0]['cargo'];
                    /////////////   Creo los Detalles   //////////////////////////////////////
                    $agentesD = $em->createQuery('SELECT l.idPersonalCargo,
                                            l.idConcepto as idConcepto,
                                            l.rGFecha as fecha,
                                            sum(l.rGCantHsGuardia) as cantidad,
                                            l.rGIdNovedad as fechaguardia,
                                            COALESCE(sum(l.montoTotalCalculado),0) as montototalcalculado
                                            FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                                        JOIN l.cuposhatipoliquidacion ca
                                        WHERE ca.id= :idCupoL and l.idPersonalCargo= :idPersonalCargo
                                        GROUP BY l.idPersonalCargo, l.idConcepto, l.rGIdNovedad, l.rGFecha')->setParameters(array('idCupoL'=> $itemC['id'],'idPersonalCargo'=>$itemC['idPersonalCargo']))->getResult();

                    //die(var_dump($agentesD));

                    foreach ($agentesD as $itemD) {

                        $fechaNovedad = $itemD['fecha'];

                        if ($fechaNovedad != null) {
                          $fechaAjuste        = $fechaNovedad->format("Y").'-'.$fechaNovedad->format("m").'-01';
                          $fechaAjusteHasta   = $fechaNovedad->format("Y").'-'.$fechaNovedad->format("m").'-'.$this->getUltimoDiaMes($fechaNovedad->format("Y"),$fechaNovedad->format("m"));
                        } else {
                          $fechaAjuste        = '';
                          $fechaAjusteHasta   = '';
                        }

                        $sql = "EXEC haberes.dbo.spInsertarDetallesSimple ".$idCargo.",".$itemD['idConcepto'].",".$itemD['cantidad'].",0,".$itemD['montototalcalculado'].",NULL,'".$vigenciaDesde."','".$vigenciaHasta."','ART48',".$ajuste.",'".$fechaAjuste."','".$fechaAjusteHasta."'";

                        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                        $stmt = $conn->prepare($sql);

                        $stmt->execute();

                    }

                    /////////////             FIN                 //////////////////////////////////////
                }

                break;
            case 24:
                foreach ($agentesC as $itemC) {

                    //inserto el cargo y me retorna el id nuevo

                    $sql = 'EXEC haberes.dbo.spInsertaCargoSimple '.$itemC['idPersonalCargo'].','.(integer)$itemC['codigo'].','.$cabecera.','.$itemC['idImputacionPresupuestaria'];

                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();

                    $idCargo = $rResult[0]['cargo'];
                    /////////////   Creo los Detalles   //////////////////////////////////////
                    $agentesD = $em->createQuery('SELECT l.idPersonalCargo,
                                            l.idConcepto as idConcepto,
                                            l.rGFecha as fecha,
                                            sum(l.rGCantHsGuardia) as cantidad,
                                            l.rGIdNovedad as fechaguardia,
                                            COALESCE(sum(l.montoTotalCalculado),0) as montototalcalculado
                                            FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                                        JOIN l.cuposhatipoliquidacion ca
                                        WHERE ca.id= :idCupoL and l.idPersonalCargo= :idPersonalCargo
                                        GROUP BY l.idPersonalCargo, l.idConcepto, l.rGIdNovedad,l.rGFecha')->setParameters(array('idCupoL'=> $itemC['id'],'idPersonalCargo'=>$itemC['idPersonalCargo']))->getResult();

                    //die(var_dump($agentesD));

                    foreach ($agentesD as $itemD) {

                        $fechaNovedad = $itemD['fecha'];

                        if ($fechaNovedad != null) {
                          $fechaAjuste        = $fechaNovedad->format("Y").'-'.$fechaNovedad->format("m").'-01';
                          $fechaAjusteHasta   = $fechaNovedad->format("Y").'-'.$fechaNovedad->format("m").'-'.$this->getUltimoDiaMes($fechaNovedad->format("Y"),$fechaNovedad->format("m"));
                        } else {
                          $fechaAjuste        = '';
                          $fechaAjusteHasta   = '';
                        }
                        $sql = "EXEC haberes.dbo.spInsertarDetallesSimple ".$idCargo.",".$itemD['idConcepto'].",".$itemD['cantidad'].",0,".$itemD['montototalcalculado'].",NULL,'".$vigenciaDesde."','".$vigenciaHasta."','Reemplazo de Guardias',".$ajuste.",'".$fechaAjuste."','".$fechaAjusteHasta."'";


                        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                        $stmt = $conn->prepare($sql);

                        $stmt->execute();

                    }

                    /////////////             FIN                 //////////////////////////////////////
                }

                break;
            case 31:
                foreach ($agentesC as $itemC) {

                    //inserto el cargo y me retorna el id nuevo

                    $sql = 'EXEC haberes.dbo.spInsertaCargoSimple '.$itemC['idPersonalCargo'].','.(integer)$itemC['codigo'].','.$cabecera.','.$itemC['idImputacionPresupuestaria'];

                    $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                    $stmt = $conn->prepare($sql);

                    $stmt->execute();
                    $rResult = 0;
                    $rResult = $stmt->fetchAll();

                    $idCargo = $rResult[0]['cargo'];
                    /////////////   Creo los Detalles   //////////////////////////////////////
                    $agentesD = $em->createQuery('SELECT l.idPersonalCargo,
                                            462 as idConcepto,
                                            l.rGFecha as fecha,
                                            sum(l.rGCantHsGuardia) as cantidad,
                                            l.rGIdNovedad as fechaguardia,
                                            COALESCE(sum(l.montoTotalCalculado),0) as montototalcalculado
                                            FROM LiquidacionesCuposAnualesBundle:Liquidaciones l
                                        JOIN l.cuposhatipoliquidacion ca
                                        WHERE ca.id= :idCupoL and l.idPersonalCargo= :idPersonalCargo
                                        GROUP BY l.idPersonalCargo, l.idConcepto, l.rGIdNovedad,l.rGFecha')->setParameters(array('idCupoL'=> $itemC['id'],'idPersonalCargo'=>$itemC['idPersonalCargo']))->getResult();

                    //die(var_dump($agentesD));

                    foreach ($agentesD as $itemD) {

                        $fechaNovedad = null;

                        if ($fechaNovedad != null) {
                          $fechaAjuste        = $fechaNovedad->format("Y").'-'.$fechaNovedad->format("m").'-01';
                          $fechaAjusteHasta   = $fechaNovedad->format("Y").'-'.$fechaNovedad->format("m").'-'.$this->getUltimoDiaMes($fechaNovedad->format("Y"),$fechaNovedad->format("m"));
                        } else {
                          $fechaAjuste        = '';
                          $fechaAjusteHasta   = '';
                        }
                        $sql = "EXEC haberes.dbo.spInsertarDetallesSimple ".$idCargo.",".$itemD['idConcepto'].",1,0,".$itemD['montototalcalculado'].",NULL,'".$vigenciaDesde."','".$vigenciaHasta."','Modulos SAT',".$ajuste.",'".$fechaAjuste."','".$fechaAjusteHasta."'";


                        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();

                        $stmt = $conn->prepare($sql);

                        $stmt->execute();

                    }

                    /////////////             FIN                 //////////////////////////////////////
                }

                break;
            default:
                break;
        }


        $request->getSession()->getFlashBag()->add(
            'aviso_ok',
            'Se liquido correctamente el cupo!'
        );
        return $this->redirect($this->generateUrl('cupos_liquidar', array('cupo' => $cupoR)));

        /////////////             FIN                 //////////////////////////////////////
    }

    /**
     * @Route("/liquidar/{cupo}/", name="cupos_liquidar")
     * @Method("GET")
     * @Template()
     */
    public function liquidarAction($cupo) {

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findBy(array("refCupo"=>$cupo));
        foreach ($entities as $cupo) {

            $nombre             = $cupo->getCuentas()->getCuenta();
            $anio               = $cupo->getCupos()->getAnio();
            $mes                = $cupo->getCupos()->getMes();
            $adicional          = $cupo->getAdicional();
            $idtipoliquidacion  = $cupo->getCuentas()->getIdTipoLiquidacion();
        }

		if ($idtipoliquidacion == 40) {
          $idtipoliquidacion = 24;
        }

        $sql =   "select ca.id, tl.tipoLiquidacion, ca.adicional "
                ." FROM        LiquidacionesHaberesBundle:HACabecera ca "
                ." join ca.tiposliquidacion tl ";
        $sql = $sql." WHERE ca.refEscenario = 7 and ca.preliquidar = '1' and tl.id = ".$idtipoliquidacion." and ca.adicional = ".$adicional;
        $consulta = $em->createQuery($sql);
        $resAdicional = $consulta->getResult();

        return array(
            'resCabeceras'  => $resAdicional,
            'guia'=> $nombre." ".$anio."-".$mes." - Adic: ".$adicional,
            'cupos'=>$cupo->getCupos()->getId(),
        );
    }

    /**
     *
     * @Route("/traerImputacion/{depe}/{cuenta}/{tipo}", name="cupos_traerImputacion")
     */
    public function traerImputacionAction($depe,$cuenta,$tipo)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $ent_cuenta = new Cuentas();
        $ent_cuenta = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($cuenta);

        $codigo = $em->getRepository('LiquidacionesHaberesBundle:HADependencias')->find($depe)->getId();

        $idTipoLiquidacion = $ent_cuenta->getIdTipoLiquidacion();
        //[Preliquidacion].[spImputacionCorrespondiente] (@IdPersonalCargo int?, @IdTipoLiquidacion int, @IdDepeForzadaHsEx int?)
        $spInsertar     = "exec haberes.Preliquidacion.spImputacionCorrespondiente null,".$idTipoLiquidacion.",".$codigo;

        $conn =  $this->getDoctrine()->getManager("ms_haberes_web")->getConnection();
        $stmt = $conn->prepare($spInsertar);
        $stmt->execute();

        $rResult = '';

        $rResult = $stmt->fetchAll();
        //die(var_dump($rResult[0]['IdImputacionPresupuestaria']));
        $vowels = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú");
        $replace = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U");

        if ($rResult[0]['IdImputacionPresupuestaria'] == 0) {
            $ar = array('id'=> 0, "imputacionDescripcion"=> '');
        } else {
            $entityImputacionPresupuestaria = $em->getRepository('LiquidacionesHaberesBundle:HAImputacionPresupuestaria')->find($rResult[0]['IdImputacionPresupuestaria']);
        //die(var_dump($entityImputacionPresupuestaria));
            $descripcion = utf8_decode($entityImputacionPresupuestaria->getDescripcion());
            $ar = array("id"=>$rResult[0]['IdImputacionPresupuestaria'], "imputacionDescripcion"=>trim($entityImputacionPresupuestaria->getProgramaDescripcion())." - ".$descripcion);
            //$ar = array("id"=>$rResult[0]['IdImputacionPresupuestaria'], "imputacionDescripcion"=>trim($entityImputacionPresupuestaria->getProgramaDescripcion())." - ".$entityImputacionPresupuestaria->getDescripcion());
            //die(var_dump($descripcion));
        }
        $response = new Response(json_encode($ar));
        $response->headers->set('content-type','application/json');

        return $response;

    }



    /**
     *
     * @Route("/traerTodosImputacion/{cuenta}", name="cupos_traerTodosImputacion")
     */
    public function traerTodosImputacionAction($cuenta)
    {

        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $ent_cuenta = new Cuentas();
        $ent_cuenta = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($cuenta);

        $idTipoLiquidacion = $ent_cuenta->getIdTipoLiquidacion();

        $entityImputacionPresupuestaria = $em->getRepository('LiquidacionesHaberesBundle:HAImputacionPresupuestaria')->findAll();

        $vowels = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú");
        $replace = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U");

        foreach ($entityImputacionPresupuestaria as $value) {
            if ($value->getActiva() == 1) {
				$ar[] = array('id'=> $value->getId(), "imputacionDescripcion"=> trim(($value->getProgramaDescripcion()))." - ".($value->getDescripcion()));
			}
        }
        $response = new Response(json_encode($ar));
        $response->headers->set('content-type','application/json');

        return $response;

    }


function showFiles($path){
      $dir = opendir($path);
      $files = array();
      while ($current = readdir($dir)){
          if( $current != "." && $current != "..") {
              if(is_dir($path.$current)) {
                  showFiles($path.$current.'/');
              }
              else {
                  $files[] = $current;
              }
          }
      }

      $cad = '';

      for($i=0; $i<count( $files ); $i++){
            $cad .= $files[$i];
        }


        return array($files,$cad);
  }

   /**
     *
     * @Route("/listararchivos", name="liquidaciones_listararchivos")
     * @Template("LiquidacionesCuposAnualesBundle:Cupos:listararchivos.html.twig")
     */
    public function listararchivosAction()
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");



        $appPath = $this->container->getParameter('kernel.root_dir');
        $webPath = realpath($appPath . '/../web/uploads/planillas');

        $archivos = $this->showFiles($webPath);

        $cad = '';
        $tabla = array();
        $nombrecuenta = '';
        $anio = '';
        $mes = '';

        foreach ($archivos[0] as $value) {

            $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find((integer)$value);
        //die(var_dump($entities));

            if (count($entities) > 0) {
              $nombrecuenta = $entities->getCuentas()->getCuenta();
              $depe = $entities->getCupos()->getIdDependencia();

              $anio = $entities->getCupos()->getAnio();
              $mes = $entities->getCupos()->getMes();


              //$cad .= "<td>".$value."</td>";
              //$cad .= "<td><a href='".str_replace("\\","/", $webPath).'/'.$this->showFiles($webPath.'/'.$value)[1]."'>Descargar</a></td>";
              $resAr = $this->showFiles($webPath.'/'.$value);
              if ($resAr[1] != '') {
                array_push($tabla, array('anio' => $anio, 'mes' => $mes ,'depe' => $depe.' - '.$nombrecuenta,'archivo'=> $resAr[1], 'carpeta'=>$value));
              }

            }
        }

        return array('tabla' => $this->array_orderby($tabla,'anio', SORT_DESC, 'mes', SORT_DESC, 'depe', SORT_ASC));
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



}
