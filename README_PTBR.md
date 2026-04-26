# Forum Portal

Este arquivo foi mantido por compatibilidade. A documentação principal em português está agora em:

- `README.md`
- `CHANGELOG_BBCODE.txt`

O `README.md` contém a descrição da extensão, instalação, recursos e changelog em Markdown.
O `CHANGELOG_BBCODE.txt` contém uma versão pronta em BBCode para postagem/anúncio no fórum.

## SEO

- Opção no ACP para impedir indexação de páginas paginadas do portal (`/portal?start=...`) usando `noindex,follow`.
- A página principal do portal continua indexável.

## Correção v1.2.4

- O bloco de enquetes não é mais ocultado pela proteção contra tópicos duplicados quando o tópico da enquete também aparece em outras áreas do portal.

### v1.2.5 - Layout / Portal CSS polish

- Alinha melhor a barra do Portal com a largura útil do conteúdo.
- Reduz espaçamentos laterais excessivos em estilos phpBB mais largos.
- Mantém o portal responsivo sem forçar uma largura fixa interna.

### v1.2.7 - Release cleanup / JS externo

- Removido JavaScript inline do template do cabeçalho.
- Criado `forumportal_home_breadcrumb.js` para manter o ajuste do breadcrumb quando o portal é usado como página inicial.
- Mantido o retorno ao índice real do fórum com `forumportal_bypass=1`.

## URL do portal e `app.php`

O Forum Portal usa o sistema de rotas do phpBB e não força URLs limpas no nível da extensão. Quando a reescrita de URL está ativada no ACP do phpBB e o servidor está configurado corretamente, a rota do portal pode aparecer sem `app.php`. Caso contrário, `app.php/portal` é a rota segura e esperada do phpBB.

### Versão 1.2.9

- As abreviações dos meses usadas pelo portal agora vêm dos arquivos de idioma, em vez de ficarem fixas no PHP.
- Isso permite que tradutores localizem corretamente os nomes dos meses em outros idiomas.
