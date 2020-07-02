<?php
/**
 * AdminPermissions type.
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AdminPermissionsType.
 */
class AdminPermissionsType extends AbstractType
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
            'role_label',
            ChoiceType::class,
            [
                'label' => 'permission_label',
                'mapped' => false,
                'choices' => [
                    'regular' => User::ROLE_USER,
                    'redactor' => User::ROLE_REDACTOR,
                ],
            ]
        );
    }
}
