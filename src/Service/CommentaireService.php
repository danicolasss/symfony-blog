<?php

namespace App\Service;

use App\Entity\Commentaire;
use App\Repository\AuteurRepository;
use App\Repository\CommentaireRepository;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;


class CommentaireService
{
    private CommentaireRepository $commentaireRepository;
    private AuteurRepository $auteurRepository;

    /**
     * @param CommentaireRepository $commentaireRepository
     * @param AuteurRepository $auteurRepository
     */
    public function __construct(CommentaireRepository $commentaireRepository, AuteurRepository $auteurRepository)
    {
        $this->commentaireRepository = $commentaireRepository;
        $this->auteurRepository = $auteurRepository;
    }


    public function addCommentaire(Commentaire $commentaire, $formCommentaire) : int
    {

        if(!$commentaire->getContenu()){
            $formCommentaire->get('contenu')->addError(new FormError('saisisez un contenu'));
            return 1;
        }


        if(!$formCommentaire->get('auteur')->get('pseudo')->isEmpty() && $formCommentaire->get('auteur')->get('nom')->isEmpty()){
            $formCommentaire->get('auteur')->get('nom')->addError(new FormError('saisir le nom'));
            return 1;
        }

        if(!$formCommentaire->get('auteur')->get('pseudo')->isEmpty() && $formCommentaire->get('auteur')->get('prenom')->isEmpty()){
            $formCommentaire->get('auteur')->get('prenom')->addError(new FormError('saisir le prénom'));
            return 1;
        }

        if($commentaire->getAuteur()->getPseudo() && !$this->auteurRepository->findOneBy(['pseudo' => $commentaire->getAuteur()->getPseudo()])){
            $this->auteurRepository->add($commentaire->getAuteur(), true);
        } elseif (!$commentaire->getAuteur()->getPseudo()){
            $commentaire->setAuteur(null);
        } else {
            $commentaire->setAuteur($this->auteurRepository->findOneBy(['pseudo' => $commentaire->getAuteur()->getPseudo()]));
        }
        $commentaire->setCreatedAt(new \DateTime());
        $this->commentaireRepository->add($commentaire, true);
        return 2;
    }

}