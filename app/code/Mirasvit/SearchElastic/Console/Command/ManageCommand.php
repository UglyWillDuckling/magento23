<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-elastic
 * @version   1.2.45
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchElastic\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ManageCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $options = [
            new InputOption('status'),
            new InputOption('reset'),
            new InputOption('get', null, InputOption::VALUE_REQUIRED),
        ];

        $this->setName('mirasvit:search-elastic:manage')
            ->setDescription('Elastic engine management')
            ->setDefinition($options);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Mirasvit\SearchElastic\Model\Engine $engine */
        $engine = $this->objectManager->create('Mirasvit\SearchElastic\Model\Engine');
        if ($input->getOption('status')) {
            $out = '';
            $result = $engine->status($out);
            if ($result) {
                $output->writeln("<comment>$out</comment>");
            } else {
                $output->writeln("<error>$out</error>");
            }
        }

        if ($input->getOption('reset')) {
            $out = '';
            $result = $engine->reset($out);
            if ($result) {
                $output->writeln("<comment>$out</comment>");
            } else {
                $output->writeln("<error>$out</error>");
            }
        }

        if ($input->getOption('get')) {
            $indices = $engine->getClient()->indices()->get(['index' => '*']);
            foreach ($indices as $indexName => $etc) {
                try {
                    $output->writeln($indexName);
                    $result = $engine->getClient()->get([
                        'type'  => 'doc',
                        'index' => $indexName,
                        'id'    => $input->getOption('get'),
                    ]);
                    print_r($result);
                } catch (\Exception $e) {
                }
            }
        }
    }
}
