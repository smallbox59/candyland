<?php

namespace App\Security\Voter;

use App\Entity\Candys;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CandysVoter extends Voter
{
    const EDIT = 'CANDY_EDIT';
    const DELETE = 'CANDY_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $candy) : bool 
    {
        if(!in_array($attribute, [self::EDIT, self::DELETE])){
            return false;
        }
        if(!$candy instanceof Candys){
            return false;
        }
        return true;

    }

    protected function voteOnAttribute($attribute, $candy, 
    TokenInterface $token): bool
    {
        //on recupere l'utilisateur à parti du token
        $user = $token->getUser();

        if(!$user instanceof UserInterface) return false;

        //on verifie si l'utilisateur est admin
        if($this->security->isGranted('ROLE_ADMIN')) return true;

        //on verifie les permissions
        switch($attribute){
            case self::EDIT:
                //on verifie si l'utilisateur peut editer
                return $this->canEdit();
                break;
            case self::DELETE:
                //on verifie si l'utilisateur peut supprimer
                return $this->canDelete();
                break;
        }
    }

    private function canEdit(){
        return $this->security->isGranted('ROLE_CANDY_ADMIN');
    }

    private function canDelete(){
        return $this->security->isGranted('ROLE_ADMIN');
    }
}