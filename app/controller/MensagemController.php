<?php
class MensagemController extends Controller
{
    /**
     * @param string $salas_id
     * @param int $limit
     */
    public function Listar($salas_id = '',$id_mensagem = '', $limit = 10){
        $token  = Token::getTokenFromHeadersOrSession('Token','Authorization');

        //Assert::equalsOrError(Usuarios::findById($token->id)->admin,true)
        // TODO:Só administradores podem visualizar as mensagen de usuarios
        if($salas_id != ''){
			$Sala = new Salas($salas_id);
				if (!$Sala->isBanido($token->id)){
					$Mensagens = (intval($id_mensagem) != null)?
					array_reverse(Mensagens::findAll(['salas_id'=>$salas_id,'id'=>$id_mensagem, '(para_id'=>$token->id], ['or'=>['para_id IS NULL', 'usuarios_id='.$token->id.')'], 'DESC'=>'id','>'=>'id','limit'=>$limit]))
						: array_reverse(Mensagens::findAll(['salas_id'=>$salas_id, '(para_id'=>$token->id], ['or'=>['para_id IS NULL', 'usuarios_id='.$token->id.')'], 'DESC'=>'id','limit'=>$limit]));	
			}
        }else{
            $Mensagens = Mensagens::findAll();
        }

        header("Content-type:application/json");
		if (isset($Mensagens)){
			echo json_encode($Mensagens);
		}
    }

    /**
     * Metodo Cadastra a Sala no banco de dados via Formulario "POST"
     */
    public function cadastrar_post(){
        $token  = Token::getTokenFromHeadersOrSession('Token','Authorization');
        $json = json_decode(file_get_contents('php://input'), true);

        //Cadastrar a mensagem
        $Mensagens = new Mensagens;
        if($json){
            $Mensagens->mensagem    = filter_var($json['mensagem'], FILTER_SANITIZE_STRING);
            $Mensagens->usuarios_id = $token->id;
            $Mensagens->salas_id    = filter_var($json['salas_id'], FILTER_SANITIZE_STRING);
            $Mensagens->para_id = filter_var($json['para_id'], FILTER_SANITIZE_STRING);
            $Mensagens->data        = date("Y-m-d H:i:s");
        }else{
            $image = null;
            if (filter_var(filter_input(INPUT_POST, 'mensagem'), FILTER_VALIDATE_URL)) {
                $image = '<img src="'.filter_input(INPUT_POST, 'mensagem').'">';
            }

            $Mensagens->mensagem = ($image) ? $image : filter_input(INPUT_POST, 'mensagem');
            $Mensagens->salas_id    = filter_input(INPUT_POST, 'salas_id');
            $Mensagens->usuarios_id = $token->id;
            $Mensagens->data        = date("Y-m-d H:i:s");
        }

        $Mensagens->save();
    }
}