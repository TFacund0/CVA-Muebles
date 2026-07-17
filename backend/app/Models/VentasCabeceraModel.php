<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Class VentasCabeceraModel
 *
 * Modelo que interactúa con la tabla 'ventas_cabecera' de la base de datos de CVA Muebles.
 * Representa los contratos principales, transacciones y metadatos de pedidos en curso del taller.
 *
 * @property int|string $id Identificador único del pedido/venta.
 * @property string $fecha Marca de tiempo del registro del pedido.
 * @property int|string $usuario_id Identificador único del usuario cliente comprador.
 * @property float|string $total_venta Importe final consolidado del pedido.
 * @property string $estado Fase de fabricación actual ('PENDIENTE', 'EN_PROCESO', 'TERMINADO', 'ENTREGADO').
 * @property string $observaciones Notas complementarias, requerimientos o tags de imágenes asociadas.
 * @property string $tipo_pedido Modalidad de ingreso ('CARRITO' = tienda web, 'MANUAL' = taller de carpintería).
 * @property string $estado_aprobacion Estado de moderación administrativa ('SOLICITUD' = presupuesto, 'ACEPTADO', 'RECHAZADO').
 * @property int|string $prioridad Puntuación numérica para el ordenamiento en la cola de trabajo de fabricación.
 * 
 * @package App\Models
 */
class VentasCabeceraModel extends Model
{
    /**
     * @var string Nombre de la tabla en la base de datos.
     */
    protected $table = 'ventas_cabecera';

    /**
     * @var string Nombre de la columna que actúa como clave primaria de la tabla.
     */
    protected $primaryKey = 'id';

    /**
     * @var array Campos de la tabla que están permitidos para su asignación e inserción masiva.
     */
    protected $allowedFields = ['fecha', 'usuario_id', 'total_venta', 'estado', 'observaciones', 'tipo_pedido', 'estado_aprobacion', 'prioridad'];

    /**
     * @var array Reglas de validación interna que aplica el modelo de forma automática antes de guardar.
     */
    protected $validationRules = [
        'usuario_id'  => 'required|numeric',
        'total_venta' => 'required|numeric|greater_than_equal_to[0]',
        'estado'      => 'required|alpha_dash'
    ];

    /**
     * Recupera listados de pedidos, filtrando opcionalmente por identificador único o por identificador
     * de cliente, incorporando información del cliente que realizó la compra mediante un join.
     * Ordena ascendentemente por prioridad y de forma cronológica descendente.
     *
     * @param int|string|null $id Identificador del pedido (opcional).
     * @param int|string|null $id_usuario Identificador del cliente (opcional).
     * 
     * @return array Listado de pedidos filtrados.
     */
    public function getVentas($id = null, $id_usuario = null)
    {
        $builder = $this->select('ventas_cabecera.id, ventas_cabecera.fecha, ventas_cabecera.usuario_id, ventas_cabecera.total_venta, ventas_cabecera.estado, ventas_cabecera.estado_aprobacion, ventas_cabecera.tipo_pedido, ventas_cabecera.observaciones, ventas_cabecera.prioridad, usuarios.nombre, usuarios.apellido, usuarios.email, usuarios.usuario')
                        ->join('usuarios', 'usuarios.id_usuario = ventas_cabecera.usuario_id', 'left');

        if ($id != null) {
            $builder->where('ventas_cabecera.id', $id);
        }

        if ($id_usuario != null) {
            $builder->where('ventas_cabecera.usuario_id', $id_usuario);
        }
        
        return $builder->orderBy('ventas_cabecera.prioridad', 'DESC')
                       ->orderBy('ventas_cabecera.fecha', 'DESC')
                       ->findAll();
    }

    public function getVentasFiltradas($search = null, $estado = null, $paginate = false, $perPage = 15)
    {
        $builder = $this->select('ventas_cabecera.id, ventas_cabecera.fecha, ventas_cabecera.usuario_id, ventas_cabecera.total_venta, ventas_cabecera.estado, ventas_cabecera.estado_aprobacion, ventas_cabecera.tipo_pedido, ventas_cabecera.observaciones, ventas_cabecera.prioridad, usuarios.nombre, usuarios.apellido, usuarios.email, usuarios.usuario')
                        ->join('usuarios', 'usuarios.id_usuario = ventas_cabecera.usuario_id', 'left');
        
        if (!empty($estado) && strtoupper($estado) === 'RECHAZADO') {
            $builder->where('ventas_cabecera.estado_aprobacion', 'RECHAZADO');
        }

        if (!empty($search)) {
            $builder->groupStart()
                        ->like('ventas_cabecera.id', $search)
                        ->orLike('usuarios.nombre', $search)
                        ->orLike('usuarios.apellido', $search)
                        ->orLike('usuarios.usuario', $search)
                    ->groupEnd();
        }

        if (!empty($estado) && strtoupper($estado) !== 'ALL') {
            $builder->where('ventas_cabecera.estado', $estado);
        }

        $builder->orderBy('ventas_cabecera.prioridad', 'DESC')
                ->orderBy('ventas_cabecera.fecha', 'DESC');

        if ($paginate) {
            return [
                'data'  => $builder->paginate($perPage, 'ventas'),
                'pager' => $this->pager
            ];
        }

        return [
            'data'  => $builder->findAll(),
            'pager' => null
        ];
    }

    /**
     * Recupera los pedidos actualmente en taller que no han sido rechazados y no están en estado
     * de solicitud de presupuesto. Se ordenan priorizando la puntuación en la cola de trabajo.
     *
     * @return array Listado de pedidos activos en taller.
     */
    public function getVentasActivas()
    {
        return $this->select('ventas_cabecera.*')
                    ->where('estado_aprobacion !=', 'RECHAZADO')
                    ->where('estado_aprobacion !=', 'SOLICITUD')
                    ->orderBy('prioridad', 'DESC')
                    ->orderBy('fecha', 'DESC')
                    ->findAll();
    }

    /**
     * Cuenta la cantidad de pedidos concretados o en curso para un mes y año calendarios específicos.
     * Excluye presupuestos rechazados.
     *
     * @param int|string $mes Número del mes (1 al 12).
     * @param int|string $anio Año calendario de consulta (4 dígitos).
     * 
     * @return int Volumen total de pedidos en el mes.
     */
    public function countMensuales($mes, $anio)
    {
        return $this->where('MONTH(fecha)', $mes)
                    ->where('YEAR(fecha)', $anio)
                    ->where('estado_aprobacion !=', 'RECHAZADO')
                    ->countAllResults();
    }

    /**
     * Cuenta la cantidad de pedidos que se encuentran transitando una fase específica de producción en el taller.
     * Excluye presupuestos rechazados.
     *
     * @param string $estado Fase de interés ('PENDIENTE', 'EN_PROCESO', 'TERMINADO', 'ENTREGADO').
     * 
     * @return int Cantidad de pedidos en la fase indicada.
     */
    public function countEstado($estado)
    {
        return $this->where('estado_aprobacion !=', 'RECHAZADO')
                    ->where('estado', $estado)
                    ->countAllResults();
    }

    public function getVentasPorUsuarioFiltradas($usuario_id, $filter = null, $sort = null)
    {
        $builder = $this->select('ventas_cabecera.*')
                        ->where('usuario_id', $usuario_id);

        if ($filter) {
            if ($filter === 'solicitado') {
                $builder->where('estado_aprobacion', 'SOLICITUD');
            } elseif ($filter === 'rechazado') {
                $builder->where('estado_aprobacion', 'RECHAZADO');
            } elseif ($filter === 'entregado') {
                $builder->whereIn('estado', ['ENTREGADO', 'TERMINADO']);
                $builder->where('estado_aprobacion !=', 'RECHAZADO');
                $builder->where('estado_aprobacion !=', 'SOLICITUD');
            } elseif ($filter !== 'todos') {
                $builder->where('estado', strtoupper($filter));
                $builder->where('estado_aprobacion !=', 'RECHAZADO');
                $builder->where('estado_aprobacion !=', 'SOLICITUD');
            }
        }

        if ($sort) {
            if ($sort === 'recent') {
                $builder->orderBy('fecha', 'DESC');
            } elseif ($sort === 'oldest') {
                $builder->orderBy('fecha', 'ASC');
            } elseif ($sort === 'high-price') {
                $builder->orderBy('total_venta', 'DESC');
            } elseif ($sort === 'low-price') {
                $builder->orderBy('total_venta', 'ASC');
            }
        } else {
            $builder->orderBy('fecha', 'DESC');
        }

        return $builder->findAll();
    }
}
