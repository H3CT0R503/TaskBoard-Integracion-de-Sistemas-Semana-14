<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Reglas del formulario de transacciones, sacadas del controlador.
 *
 * Antes esto era un validate() dentro de store(). Como clase aparte se
 * reutiliza tal cual en update(), y el controlador queda con la lógica
 * del flujo nada más.
 */
class GuardarTransaccionRequest extends FormRequest
{
    /**
     * Responde si quien envía la petición tiene permiso, que es una
     * pregunta distinta a si los datos son correctos.
     *
     * Artisan lo genera en false y eso devuelve 403. Va en true
     * porque la pregunta de quién entra ya la responde el middleware
     * auth de la ruta: sin sesión, la petición no llega hasta este
     * Form Request.
     *
     * Y la del dueño del comercio, que la semana pasada quedó
     * pendiente justo acá, se resolvió dentro de rules(): el
     * comercio_id se valida contra los comercios del usuario de la
     * sesión, que es más preciso que un true o false para toda la
     * petición y además devuelve un mensaje de validación en vez de
     * un 403 seco.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de cada campo.
     *
     * La de comercio_id es la que importa de más, y esta semana
     * cambió. Antes era exists:comercios,id, que preguntaba solo si
     * el comercio existía: con eso, cambiar el número del campo
     * oculto desde las herramientas del navegador alcanzaba para
     * colgarle una transacción al comercio de otra persona.
     *
     * Ahora el exists lleva un where user_id, así que el comercio
     * tiene que existir Y ser del usuario de la sesión. Filtrar el
     * <select> del formulario no sustituye esto: el navegador es del
     * atacante, y una opción borrada de la lista se vuelve a escribir
     * en dos clics o se manda la petición sin formulario. Lo único
     * que de verdad decide es la validación del servidor.
     *
     * $this->user() dentro de un Form Request es el usuario
     * autenticado, el mismo que devolvería Auth::user().
     *
     * En la Semana 13 esta regla tuvo que partirse en dos con un if,
     * porque el mismo Form Request empezó a validar también las
     * peticiones de la API y ahí $this->user() era null: las rutas de
     * routes/api.php no usaban sesión, así que leerle ->id reventaba
     * con un 500 antes de poder validar nada. Sin usuario la regla
     * caía a un exists simple, que comprobaba solo que el comercio
     * existiera, y por esa rama cualquiera podía colgarle una
     * transacción al comercio de otro.
     *
     * Esta semana el if se va, y eso es lo que mejor mide lo que
     * cambió: dentro de auth:sanctum SIEMPRE hay usuario, porque
     * cada petición llega firmada con un token y Sanctum lo resuelve
     * antes de que el Form Request corra. $this->user() dentro de un
     * Form Request es el usuario autenticado, venga de la sesión del
     * navegador o del token de la API, así que la regla vuelve a ser
     * una sola y la rama insegura desaparece del archivo.
     *
     * Dejar el if "por si acaso" sería peor que quitarlo: una rama
     * que ya no se puede alcanzar igual se lee como una opción
     * válida, y el día que alguien saque una ruta del grupo
     * auth:sanctum, la validación se relajaría sola y en silencio. Si
     * eso pasa, ahora reventará con un error en vez de dejar pasar
     * datos ajenos, que es el fallo correcto.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'comercio_id' => [
                'required',
                Rule::exists('comercios', 'id')
                    ->where('user_id', $this->user()->id),
            ],
            'cliente_nombre' => ['required', 'string', 'max:255'],
            'monto' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    /**
     * Mensajes escritos a mano, para que el error explique qué hacer
     * en lugar de nombrar la regla que falló.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cliente_nombre.required' => 'Debes indicar el nombre del cliente.',
            'monto.required' => 'Debes indicar un monto.',
            'monto.numeric' => 'El monto debe ser un número.',
            'monto.min' => 'El monto debe ser mayor a cero.',
            // El exists falla igual si el comercio no existe o si es
            // de otra persona, y el mensaje no distingue los dos
            // casos a propósito: no tiene por qué confirmar que el
            // comercio 7 existe y es de alguien más.
            'comercio_id.exists' => 'Ese comercio no está disponible en tu cuenta.',
        ];
    }

    /**
     * Nombres legibles para los mensajes que dejé por defecto. Con
     * esto el error dice "comercio" y no "comercio_id".
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'cliente_nombre' => 'nombre del cliente',
            'monto' => 'monto de la transacción',
            'comercio_id' => 'comercio',
        ];
    }
}
