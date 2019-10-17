<?php

namespace Cnab\Format;

class Field
{
    private $cnabLinha;
    public $nome;
    public $format;
    private $valor_decoded;
    private $valor_encoded = null;

    public $pos_start;
    public $pos_end;
    public $length;
    public $options;

    public function __construct(Linha $linha, $nome, $format, $pos_start, $pos_end, $options)
    {
        if (!Picture::validarFormato($format)) {
            throw new \InvalidArgumentException("'$format' não é um ofrmato válido $nome");
        }

        $this->options = $options;
        $this->nome = $nome;
        $this->cnabLinha = $linha;
        $this->format = $format;
        $this->pos_start = $pos_start;
        $this->pos_end = $pos_end;
        $this->length = ($pos_end + 1) - $pos_start;

        $p_length = Picture::getLength($this->format);
        if ($p_length > $this->length) {
            throw new \Exception("Picture length of '$this->nome' need more positions than  $pos_start : $pos_end");
        } elseif ($p_length < $this->length) {
            throw new \Exception("Picture length of '$this->nome' need less positions than  $pos_start : $pos_end");
        }
    }

    public function set($valor)
    {
        if ($valor === false || is_null($valor)) {
            throw new \Exception("'$this->nome' não pode ser false ou null");
        }

        $this->valor_decoded = $valor;

        try {
            $this->valor_encoded = Picture::encode($valor, $this->format, $this->options);
        } catch (\Exception $e) {
            trigger_error("Erro no campo '$this->nome': ".$e->getMessage(), E_USER_NOTICE);
            throw $e; // para exibir o backtrace
        }

        $len = strlen($this->valor_encoded);
        if ($len != $this->length) {
            throw new \Exception("'$this->nome' tamanho atual '$len', mas precisa do tamanho $this->length");
        }
    }

    public function getValue()
    {
        return $this->valor_decoded;
    }

    public function getName()
    {
        return $this->nome;
    }

    public function getEncoded()
    {
        if (is_null($this->valor_encoded)) {
            throw new \Exception("'$this->nome' não pode ser null, precisa ser preenchido");
        }

        return $this->valor_encoded;
    }
}
