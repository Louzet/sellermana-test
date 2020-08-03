<?php

namespace App\Form\Game;

use App\Entity\Game;
use App\Entity\State;
use App\Entity\Concurrent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\DataTransformer\StateToStringTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class GameCreationType extends AbstractType
{
    private $em;

    private $transformer;

    public function __construct(EntityManagerInterface $entityManagerInterface, StateToStringTransformer $transformer)
    {
        $this->em = $entityManagerInterface;
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $concurrents = $this->em->getRepository(Concurrent::class)->findAll();

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('concurrent', ChoiceType::class, [
                'required' => true,
                'choices' => $concurrents,
                'choice_label' => function (Concurrent $concurrent) {
                    return $concurrent ? $concurrent->getName() : '';
                },
                'attr' => ['class' => 'form-control']
            ])
            ->add('price', HiddenType::class, [
                'required' => false,
                'empty_data' => 0,
                'attr' => ['class' => 'form-control']
            ])
            ->add('floorPrice', NumberType::class, [
                'required' => true,
                'empty_data' => 0,
                'attr' => ['class' => 'form-control']
            ])
            ->add('maxPrice', NumberType::class, [
                'required' => true,
                'empty_data' => 0,
                'attr' => ['class' => 'form-control']
            ])
            ->add('state', ChoiceType::class, [
                'required' => true,
                'choices' => State::$states,
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'form-control btn btn-primary mt-3']
            ])
        ;

        $builder
                ->get('state')
                ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
