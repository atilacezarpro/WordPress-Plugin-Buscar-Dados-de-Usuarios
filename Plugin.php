<?php
/*
Plugin Name: Busca User Data
Description: Plugin para busca de usuários cadastrados
Version: 1.0
Author: Atila Rodrigues
*/

// Função para adicionar os campos de busca no formulário
function busca_user_data_form_shortcode() {
    ob_start();
    ?>
    <div style="background-color: #ECECEC; padding: 20px;">
        <input type="text" id="searchInput" style="width: 80%; padding: 10px; border: 1px solid #949494; box-shadow: none; outline: none; color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 300; text-transform: uppercase;" placeholder="Digite o termo de busca">
        <button id="searchButton" style="width: 18%; padding: 10px; font-family: 'Roboto', sans-serif; font-weight: 500; background-color: #007C11; border: 1px solid #007C11; color: #FFFFFF; text-transform: uppercase;">Buscar</button>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('busca_user_data_form', 'busca_user_data_form_shortcode');

// Função para exibir os resultados da busca
function busca_user_data_results_shortcode() {
    ob_start();
    ?>
    <div style="background-color: #ECECEC; padding: 20px;">
        <h2 style="color: #008815; font-family: 'Roboto', sans-serif; font-size: 25px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.7px; margin-bottom: 10px;">Resultados</h2>
        <div id="searchResults"></div>
    </div>
    <style>
        .user-result {
            background-color: #ECECEC;
            padding: 20px;
            margin-bottom: 20px;
        }

        .user-result h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #7A7A7A;
            font-family: "Roboto", sans-serif;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .user-result p {
            margin-bottom: 5px;
            color: #1D1D1D;
            font-family: "Roboto", sans-serif;
            font-size: 16px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .user-result .button-container {
			display: flex;
			justify-content: center;
		}

		.user-result button {
			font-family: 'Roboto', sans-serif;
			font-weight: 500;
			background-color: #007C11;
			border: none;
			color: #FFFFFF;
			cursor: pointer;
			margin: 15px;
			padding: 12px;
			text-transform: uppercase;
			font-size: 16px;
		}

        .user-result button:hover {
            background-color: #002405;
            border-color: #002405;
        }
    </style>
    <script>
        // Função para atualizar a situação do usuário
        function updateSituacao(userId, situacao) {
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'busca_user_data_update_situacao',
                    userId: userId,
                    situacao: situacao
                },
                success: function (response) {
                    console.log(response.data);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        // Função para lidar com o clique no botão "Ausente"
        jQuery(document).on('click', '.ausenteButton', function () {
            var userId = jQuery(this).data('user-id');
            updateSituacao(userId, 'Não compareceu à prova');
        });

        // Função para lidar com o clique no botão "Matricular"
        jQuery(document).on('click', '.matricularButton', function () {
            var userId = jQuery(this).data('user-id');
            updateSituacao(userId, 'Já efetuou a matrícula');
        });

        // Função para buscar os usuários
        function searchUsers() {
            var searchInput = jQuery('#searchInput').val();

            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'busca_user_data_search_users',
                    searchInput: searchInput
                },
                success: function (response) {
                    if (response.success) {
                        jQuery('#searchResults').html(response.data);
                    } else {
                        jQuery('#searchResults').html('<p>' + response.data + '</p>');
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        // Função para lidar com o clique no botão de busca
        jQuery('#searchButton').on('click', function () {
            searchUsers();
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('busca_user_data_results', 'busca_user_data_results_shortcode');



// Função para lidar com a requisição AJAX de busca de usuários
add_action('wp_ajax_busca_user_data_search_users', 'busca_user_data_ajax_search_users');
add_action('wp_ajax_nopriv_busca_user_data_search_users', 'busca_user_data_ajax_search_users');

function busca_user_data_ajax_search_users() {
    $search_input = sanitize_text_field($_POST['searchInput']);
    $args = array(
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'cpf',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'firstname',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'inscricao',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'curso_escolhido',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'turno',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'modelo_prova',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'gabarito_recebido',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'gabarito_oficial',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'acertos',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'erros',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'espelho_gabarito',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'proposta_redacao',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'espelho_redacao',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'nota_prova_objetiva',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'nota_redacao',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'nota_final',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'bolsa_concedida',
                'value' => $search_input,
                'compare' => 'LIKE'
            ),
        ),
    );
    $users = get_users($args);

    if ($users) {
        ob_start();
        foreach ($users as $user) {
            $user_id = $user->ID;
            $cpf = get_user_meta($user_id, 'cpf', true);
            $firstname = get_user_meta($user_id, 'first_name', true);
            $inscricao = get_user_meta($user_id, 'inscricao', true);
            $curso_escolhido = get_user_meta($user_id, 'curso_escolhido', true);
            $turno = get_user_meta($user_id, 'turno', true);
            $modelo_prova = get_user_meta($user_id, 'modelo_prova', true);
            $gabarito_recebido = get_user_meta($user_id, 'gabarito_recebido', true);
            $gabarito_oficial = get_user_meta($user_id, 'gabarito_oficial', true);
            $acertos = get_user_meta($user_id, 'acertos', true);
            $erros = get_user_meta($user_id, 'erros', true);
            $espelho_gabarito = get_user_meta($user_id, 'espelho_gabarito', true);
            $proposta_redacao = get_user_meta($user_id, 'proposta_redacao', true);
            $espelho_redacao = get_user_meta($user_id, 'espelho_redacao', true);
            $nota_prova_objetiva = get_user_meta($user_id, 'nota_prova_objetiva', true);
            $nota_redacao = get_user_meta($user_id, 'nota_redacao', true);
            $nota_final = get_user_meta($user_id, 'nota_final', true);
            $bolsa_concedida = get_user_meta($user_id, 'bolsa_concedida', true);
            $situacao = get_user_meta($user_id, 'situacao', true);
            ?>
            <div class="user-result">
    <table border="0" width="100%">
        <tr>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Nome:</strong></td>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">CPF:</strong></td>
        </tr>
        <tr>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $firstname; ?></td>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $cpf; ?></td>
        </tr>
    </table>
    <table border="0" width="100%">
        <tr>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Inscrição:</strong></td>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Curso Escolhido:</strong></td>
        </tr>
        <tr>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $inscricao; ?></td>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $curso_escolhido; ?></td>
        </tr>
    </table>
    <table border="0" width="100%">
        <tr>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Turno:</strong></td>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Modelo de Prova:</strong></td>
        </tr>
        <tr>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $turno; ?></td>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $modelo_prova; ?></td>
        </tr>
    </table>
    <table border="0" width="100%">
        <tr>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Gabarito Recebido:</strong></td>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Gabarito Oficial:</strong></td>
        </tr>
        <tr>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $gabarito_recebido; ?></td>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $gabarito_oficial; ?></td>
        </tr>
    </table>
    <table border="0" width="100%">
        <tr>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Acertos:</strong></td>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Erros:</strong></td>
        </tr>
        <tr>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $acertos; ?></td>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $erros; ?></td>
        </tr>
    </table>
    <table border="0" width="100%">
        <tr>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Espelho de Gabarito:</strong></td>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Proposta de Redação:</strong></td>
        </tr>
        <tr>
            <td><a href="<?php echo $espelho_gabarito; ?>" style="color: #007c11; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 600; text-transform: uppercase;">Ver gabarito do candidato</a></td>
            <td><a href="<?php echo $proposta_redacao; ?>" style="color: #007c11; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 600; text-transform: uppercase;">Ver a proposta de redação</a></td>
        </tr>
    </table>
    <table border="0" width="100%">
        <tr>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Espelho de Redação:</strong></td>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Nota da Prova Objetiva:</strong></td>
        </tr>
        <tr>
           <td>
    <a href="<?php echo $espelho_redacao; ?>" style="color: #007c11; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 600; text-transform: uppercase;">
        Ver a redação do candidato
    </a>
</td>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $nota_prova_objetiva; ?></td>
        </tr>
    </table>
    <table border="0" width="100%">
        <tr>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Nota da Redação:</strong></td>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Nota Final:</strong></td>
        </tr>
        <tr>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $nota_redacao; ?></td>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $nota_final; ?></td>
        </tr>
    </table>
    <table border="0" width="100%">
        <tr>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Bolsa Concedida:</strong></td>
            <td><strong style="color: #7A7A7A; font-family: 'Roboto', sans-serif; font-size: 14px; font-weight: 500; text-transform: uppercase;">Situação:</strong></td>
        </tr>
        <tr>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $bolsa_concedida; ?></td>
            <td style="color: #1D1D1D; font-family: 'Roboto', sans-serif; font-size: 16px; font-weight: 500; text-transform: uppercase;"><?php echo $situacao; ?></td>
        </tr>
    </table>
    <div class="button-container">
        <button class="ausenteButton" data-user-id="<?php echo $user_id; ?>">Ausente</button>
        <button class="matricularButton" data-user-id="<?php echo $user_id; ?>">Matricular</button>
    </div>
</div>


            <?php
        }
        $response = array(
            'success' => true,
            'data' => ob_get_clean()
        );
    } else {
        $response = array(
            'success' => false,
            'data' => 'Nenhum usuário encontrado.'
        );
    }

    wp_send_json($response);
}

// Função para lidar com a requisição AJAX de atualização da situação do usuário
add_action('wp_ajax_busca_user_data_update_situacao', 'busca_user_data_ajax_update_situacao');
add_action('wp_ajax_nopriv_busca_user_data_update_situacao', 'busca_user_data_ajax_update_situacao');

function busca_user_data_ajax_update_situacao() {
    $user_id = intval($_POST['userId']);
    $situacao = sanitize_text_field($_POST['situacao']);

    update_user_meta($user_id, 'situacao', $situacao);

    $response = array(
        'success' => true,
        'data' => 'Situação atualizada com sucesso.'
    );

    wp_send_json($response);
}
?>
