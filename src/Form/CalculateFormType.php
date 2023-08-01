<?php

namespace App\Form;

use App\Entity\PaymentProcessor;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CalculateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('product', EntityType::class, ['class' => Product::class, 'required' => true])
        ->add('tax_number', TextType::class,['attr' => ['placeholder' => 'Tax number']])
        ->add('coupon_code', TextType::class, ['required' => false, 'attr' => ['placeholder' => 'Coupon code']])
        ->add('payment_processor', EntityType::class, ['class' => PaymentProcessor::class, 'required' => true]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['id' => 'calculate_form']
        ]);
    }
}