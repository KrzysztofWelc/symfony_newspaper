<?php
/**
 * SuperAdminPermissions type.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\User;

/**
 * Class SuperAdminPermissionsType.
 */
class SuperAdminPermissionsType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array                                        $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'role',
            ChoiceType::class,
            [
                'label' => 'role_label',
                'mapped' => false,
                'choices' => [
                    'regular' => User::ROLE_USER,
                    'redactor' => User::ROLE_REDACTOR,
                    'admin' => User::ROLE_ADMIN,
                ],
            ]
        );
    }
}
