<?php

namespace App\Service\Payment;

use App\Entity\CreditCard as EntityCreditCard;
use App\Entity\DataCrypt;
use App\Entity\User;
use App\Service\Abstract\AbstractService;

class CreditCard extends AbstractService {

    public function addCreditCard(EntityCreditCard $creditCard, User $user)
    {
        $key = $this->params->get('app.encrypt_key');
        $cipher = $this->params->get('app.cipher');
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
        $tag = base64_encode($this->params->get('app.tag'));

        $crypt = openssl_encrypt($creditCard->getNumber(), $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);

        $creditCard->setNumber(base64_encode($crypt));
        $creditCard->setAuthor($user);
        $this->creditCardRepository->save($creditCard, true);

        $dataCrypt = new DataCrypt();
        $dataCrypt->setCreditCard($creditCard);
        $dataCrypt->setIv(base64_encode($iv));
        $dataCrypt->setTag(base64_encode($tag));
        $this->dataCryptRepository->save($dataCrypt, true);
    }

    public function decryptCreditCards(array $creditCards): array
    {
        if (empty($creditCards)) {
            return [];
        }
        $key = $this->params->get('app.encrypt_key');
        $cipher = $this->params->get('app.cipher');
        $tag = $this->params->get('app.tag');
        $data = [];
        foreach ($creditCards as $creditCard) {
            $crypt = base64_decode($creditCard->getNumber());
            $dataCrypt = $this->dataCryptRepository->findOneBy(['creditCard' => $creditCard]);
            $iv = base64_decode($dataCrypt->getIv());
            $tag = base64_decode($dataCrypt->getTag());

            $data[] = [
                'id' => $creditCard->getId(),
                'name' => $creditCard->getName(),
                'number' => substr(openssl_decrypt($crypt, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag), 12, 4),
                'expiration' => $creditCard->getExpiration(),
                'selected' => $creditCard->isSelected(),
            ];
        }
        return $data;
    }

    public function decryptOneForStripe(EntityCreditCard $creditCard): array
    {
        $key = $this->params->get('app.encrypt_key');
        $cipher = $this->params->get('app.cipher');
        $tag = $this->params->get('app.tag');
        $crypt = base64_decode($creditCard->getNumber());
        $dataCrypt = $this->dataCryptRepository->findOneBy(['creditCard' => $creditCard]);
        $iv = base64_decode($dataCrypt->getIv());
        $tag = base64_decode($dataCrypt->getTag());

        $data = [
            'id' => $creditCard->getId(),
            'name' => $creditCard->getName(),
            'number' => openssl_decrypt($crypt, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag),
            'expiration' => $creditCard->getExpiration(),
            'securityCode' => $creditCard->getSecurityCode(),
            'selected' => $creditCard->isSelected(),
        ];
        return $data;
    }
}