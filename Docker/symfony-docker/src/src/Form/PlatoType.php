<?php

namespace App\Form;

use App\Entity\Plato;
use App\Enum\TipoPlato;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulario para añadir un plato. Los datos van directamente a un objeto Plato.
 */
class PlatoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, ['attr' => ['placeholder' => 'Nombre del plato']])
            ->add('tipo', EnumType::class, [
                'class' => TipoPlato::class,
                'choice_label' => fn (TipoPlato $tipo) => $tipo->titulo(),
            ])
            ->add('precio', NumberType::class, ['scale' => 2, 'input' => 'string', 'html5' => true,
                'attr' => ['step' => '0.01', 'min' => 0]])
            ->add('vegano', CheckboxType::class, ['required' => false])
            ->add('guardar', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Plato::class]);
    }
}
