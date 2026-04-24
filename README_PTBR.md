# Forum Portal (MVP inicial)

Extensão experimental para phpBB 3.3.x que transforma um fórum específico em uma página estilo portal/revista.

## O que esta base já inclui

- rota `/portal`
- opção no ACP para ativar o portal
- opção para usar o portal como página inicial
- escolha de um fórum de origem
- cards com imagem, título, meta, resumo e botão `Leia mais`
- bloco HTML personalizado no topo ou rodapé
- campos no posting do primeiro post para:
  - publicar ou não no portal
  - URL externa da imagem
  - resumo personalizado
- idiomas `pt_br` e `en`

## O que ainda deve crescer nas próximas versões

- destaque manual pelo ACP
- múltiplos fóruns de origem
- captura automática da primeira imagem/anexo
- paginação do portal
- botão publicar/remover direto no viewtopic
- layouts alternativos
- refinamentos de CSS e ACP

## Observações honestas

Esta é uma base inicial para a saga do projeto. A estrutura foi montada para crescer bem, mas ainda deve receber ajustes finos conforme os testes reais no phpBB.


## Atualização
- Adicionado hook de integração `mundophpbb_forumportal_after_content` em `portal_body.html` para blocos extras no portal, como GitHub Portfolio.
