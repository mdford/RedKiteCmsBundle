<?php

namespace RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Command\Generate;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use RedKiteLabs\RedKiteCms\RedKiteCmsBundle\Core\Generator\AppBlockGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class GenerateAppBlockBundleCommand extends BaseGenerateBundle
{
    protected $updateKernel = false;

    protected function configure()
    {
        $this
            ->setName('redkitecms:generate:app-block')
            ->setDescription('Generate a RedKiteCMs App-Block bundle')
            ->setDefinition(array(
                new InputOption('namespace', '', InputOption::VALUE_REQUIRED, 'The namespace of the bundle to create'),
                new InputOption('dir', '', InputOption::VALUE_REQUIRED, 'The directory where to create the bundle'),
                new InputOption('bundle-name', '', InputOption::VALUE_REQUIRED, 'The optional bundle name'),
                new InputOption('format', '', InputOption::VALUE_REQUIRED, 'Do nothing but mandatory for extend', 'annotation'),
                new InputOption('structure', '', InputOption::VALUE_NONE, 'Whether to generate the whole directory structure'),
                new InputOption('no-strict', '', InputOption::VALUE_NONE, 'Skips the strict control on App-Block namespace'),
                new InputOption('description', '', InputOption::VALUE_REQUIRED, 'The App-Block description displayed in the add-block menu'),
                new InputOption('group', '', InputOption::VALUE_REQUIRED, 'The App-Block group, to group thogether blocks'),

            ));
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        parent::interact($input, $output);

        $dialog = $this->getDialogHelper();

        $output->writeln(array(
            '',
            'Please enter the description that identifies your App-Block content.',
            'The value you enter will be displayed in the adding menu.',
            '',
        ));
        $description = $dialog->ask($output, $dialog->getQuestion('App-Block description', $input->getOption('description')), $input->getOption('description'));
        $input->setOption('description', $description);

        $output->writeln(array(
            '',
            'Please enter the group name to keep toghether the App-Blocks that belongs that group.',
            '',
        ));
        $group = $dialog->ask($output, $dialog->getQuestion('App-Block group', $input->getOption('group')), $input->getOption('group'));
        $input->setOption('group', $group);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $output->writeln(array(
            '',
            'Please clear your cache to have your App-Block working',
            '',
        ));
    }

    protected function checkStrictNamespace($namespace)
    {
        if (preg_match('/^RedKiteCms\\\\Block\\\\[\w]+Bundle/', $namespace) == false) {
            throw new \RuntimeException('A strict RedKiteCms App-Block namespace must start with RedKiteCms\Block suffix');
        }
    }

    protected function getGeneratorExtraOptions(InputInterface $input)
    {
        return array(
            'description' => $input->getOption('description'),
            'group' => $input->getOption('group'),
            'no-strict' => $input->getOption('no-strict'),
        );
    }

    protected function getGenerator(BundleInterface $bundle = null)
    {
        if (null === $this->generator) {
            // @codeCoverageIgnoreStart
            $kernel = $this->getContainer()->get('kernel');
            $bundlePath = $kernel->locateResource('@SensioGeneratorBundle');

            return new AppBlockGenerator($this->getContainer()->get('filesystem'), $bundlePath.'/Resources/skeleton/bundle');
            // @codeCoverageIgnoreEnd
        }

        return $this->generator;
    }
}
